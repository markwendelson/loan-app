<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller {

    public function index() {
        $data = [];
        $departments = Department::orderBy('name', 'asc')->get();

        //compile data
        $data = [
            'title' => 'Department',
            'departments' => $departments
        ];
        return view('department.index', $data);
    }

    public function getDepartmentInfo(Request $request){
        $data = [];

        $inputData = $request->post();

        $departmentData = Department::getDepartments([
            'id' => [$inputData['id']]
        ]);

        if($departmentData){
            $data = [
                'has_error' => 0,
                'data' => $departmentData
            ];
        }else{
            $data = $this->generateNotifications("'");
        }

        return response()->json($data);
    } 

    public function submitDepartment(Request $request) {
        $data = [];

        $inputData = $request->post();

        //check for add function
        if(isset($inputData['id']) && $inputData['id']){
            //update function
            
            //check first duplicate
            $checkDuplicate = Department::getDepartments([
                'not_id' => [$inputData['id']],
                'name' => $inputData['name']
            ]);

            if($checkDuplicate){
                $data = $this->generateNotifications(3);
                return response()->json($data);
            }

            $query = Department::findOrFail($inputData['id']);
            $result = $query->update($inputData);

            $departments = Department::orderBy('name', 'asc')->get();

            $html = view('department.index_data', ['departments' => $departments])->render();

            if($result){
                $data = $this->generateNotifications(2);
            }else{
                $data = $this->generateNotifications("");
            }
        }else{
            //add function

            //check first duplicate
            $checkDuplicate = Department::getDepartments([
                'name' => $inputData['name']
            ]);

            if($checkDuplicate){
                $data = $this->generateNotifications(3);
                return response()->json($data);
            }

            // Fetch the updated department list after adding
            // Create a new department
            $result = Department::create($inputData);

             // Fetch the updated department list after adding
            $departments = Department::orderBy('name', 'asc')->get();
            $html = view('department.index_data', ['departments' => $departments])->render();

            if($result){
                $data = $this->generateNotifications(1);
            }else{
                $data = $this->generateNotifications("");
            }
        }

        //insert rendered table data
        if(isset($html) && $html){
            $data['html'] = $html; 
        }

        return response()->json($data);
    }

    public function generateNotifications($type){
        //default error
        $data = [
            'has_error' => 1,
            'title' => 'Error',
            'message' => 'An error occurred while trying to add the new department. Please try again.',
            'type' => 'error'
        ];

        if($type && $type == 1){
            //add notification success
            $data = [
                'has_error' => 0,
                'title' => 'Success',
                'message' => 'The new department has been successfully added.',
                'type' => 'success'
            ];
        }

        if($type && $type == 2){
            //update notification success
            $data = [
                'has_error' => 0,
                'title' => 'Update Successful',
                'message' => 'The department details have been successfully updated.',
                'type' => 'success'
            ];
        }

        if($type && $type == 3){
            //update notification success
            $data = [
                'has_error' => 1,
                'title' => 'Duplicate Record',
                'message' => 'A department with these details already exists.',
                'type' => 'error'
            ];
        }


        return $data;
    }


}   