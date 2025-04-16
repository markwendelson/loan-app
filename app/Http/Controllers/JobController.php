<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\JobTitle; // Make sure to import the Job model


class JobController extends Controller
{
    public function index() {
        $data = [];
        $jobs = JobTitle::orderBy('title', 'asc')->get();

        //compile data
        $data = [
            'title' => 'Job Title',
            'jobs' => $jobs
        ];
        return view('job.index', $data);
    }

    public function getJobInfo(Request $request){
        $data = [];

        $inputData = $request->post();

        $jobData = JobTitle::getJobTitles([
            'id' => [$inputData['id']]
        ]);

        if($jobData){
            $data = [
                'has_error' => 0,
                'data' => $jobData
            ];
        }else{
            $data = $this->generateNotifications("'");
        }

        return response()->json($data);
    } 

    public function submitJob(Request $request) {
        $data = [];

        $inputData = $request->post();
           
        //check for add function
        if(isset($inputData['id']) && $inputData['id']){
            //update function
            
            //check first duplicate
            $checkDuplicate = JobTitle::getJobTitles([
                'not_id' => [$inputData['id']],
                'title' => $inputData['title']
            ]);

            if($checkDuplicate){
                $data = $this->generateNotifications(3);
                return response()->json($data);
            }

            $query = JobTitle::findOrFail($inputData['id']);
            $result = $query->update($inputData);

            $jobs = JobTitle::orderBy('title', 'asc')->get();

            $html = view('job.index_data', ['jobs' => $jobs])->render();

            if($result){
                $data = $this->generateNotifications(2);
            }else{
                $data = $this->generateNotifications("");
            }
        }else{
            //add function
            //check first duplicate
            $checkDuplicate = JobTitle::getJobTitles([
                'title' => $inputData['title']
            ]);

            if($checkDuplicate){
                $data = $this->generateNotifications(3);
                return response()->json($data);
            }

            $result = JobTitle::create($inputData);

            $jobs = JobTitle::orderBy('title', 'asc')->get();
            $html = view('job.index_data', ['jobs' => $jobs])->render();

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