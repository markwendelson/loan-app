<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\InsuranceFeeSched; // Make sure to import the Job model


class InsuranceFeeController extends Controller
{
    public function index() {
        $data = [];
        // $insFeeSched = InsuranceFeeSched::orderBy('min_amount', 'ASC')->get();

        // Fetch the unread notifications for the logged-in user
        // $notifications = auth()->user()->unreadNotifications;

        // // Mark notifications as read
        // $notifications->markAsRead();

        //compile data
        $data = [
            'title' => 'Insurance Fee Schedule',
            // 'insFeeSched' => $insFeeSched,
            'insFeeSched' => InsuranceFeeSched::orderBy('min_amount', 'ASC')->get()
        ];

        
            return view('insurance_fee.index', $data);        
        }

    public function getInsuranceFeeInfo(Request $request){
        $data = [];

        $inputData = $request->post();

        $insuranceFeeData = InsuranceFeeSched::getInsFeeSched([
            'id' => [$inputData['id']]
        ]);

        if($insuranceFeeData){
            $data = [
                'has_error' => 0,
                'data' => $insuranceFeeData
            ];
        }else{
            $data = $this->generateNotifications("'");
        }

        return response()->json($data);
    } 


    public function submitInsFeeSched(Request $request) {
        $data = [];

        $inputData = $request->post();
        
        // Remove commas from numeric fields
        $inputData['min_amount'] = str_replace(',', '', $inputData['min_amount']);
        $inputData['max_amount'] = str_replace(',', '', $inputData['max_amount']);
        $inputData['monthly_amount'] = str_replace(',', '', $inputData['monthly_amount']);
        $inputData['annual_amount'] = str_replace(',', '', $inputData['annual_amount']);

        // dd($inputData);

        //check for add function
        if(isset($inputData['id']) && $inputData['id']){
            //update function
            //check first duplicate
            $checkDuplicate = InsuranceFeeSched::getInsFeeSched([
                // 'not_id' => [$inputData['id']],
                'min_amount' => $inputData['min_amount']
            ]);

            if($checkDuplicate){
                $data = $this->generateNotifications(3);
                return response()->json($data);
            }

            $query = InsuranceFeeSched::findOrFail($inputData['id']);
            $result = $query->update($inputData);

            $insFeeSched = InsuranceFeeSched::orderBy('min_amount', 'ASC')->get();

            $html = view('insurance_fee.index_data', ['insFeeSched' => $insFeeSched])->render();

            if($result){
                $data = $this->generateNotifications(2);
            }else{
                $data = $this->generateNotifications("");
            }
        }else{
            
            //add function ------------------------------------------------------------------------
            //check first duplicate
            $checkDuplicate = InsuranceFeeSched::getInsFeeSched([
                'min_amount' => $inputData['min_amount']
            ]);

            if($checkDuplicate){
                $data = $this->generateNotifications(3);
                return response()->json($data);
            }

            // Fetch the updated InsFee list after adding
            // Create a new InsFeeSched
            $result = InsuranceFeeSched::create($inputData);
         
             // Fetch the updated department list after adding
            $insFeeSched = InsuranceFeeSched::orderBy('min_amount', 'ASC')->get();
            $html = view('insurance_fee.index_data', ['insFeeSched' => $insFeeSched])->render();

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
                'message' => 'The new Insurance Fee Schedulehas been successfully added.',
                'type' => 'success'
            ];
        }

        if($type && $type == 2){
            //update notification success
            $data = [
                'has_error' => 0,
                'title' => 'Update Successful',
                'message' => 'The Insurance Fee Schedule have been successfully updated.',
                'type' => 'success'
            ];
        }

        if($type && $type == 3){
            //update notification success
            $data = [
                'has_error' => 1,
                'title' => 'Duplicate Record',
                'message' => 'Insurance Fee Schedule already exists.',
                'type' => 'error'
            ];
        }


        return $data;
    }


}