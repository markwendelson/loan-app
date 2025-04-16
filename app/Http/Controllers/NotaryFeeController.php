<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\NotaryFeeSched; // Make sure to import


class NotaryFeeController extends Controller
{
    
    public function index() {
        $data = [];
        // $insFeeSched = InsuranceFeeSched::orderBy('min_amount', 'ASC')->get();
   
        //compile data
        $data = [
            'title' => 'Notary Fee Schedule',
            // 'insFeeSched' => $insFeeSched,
            'notaryFeeSched' => NotaryFeeSched::orderBy('min_amount', 'ASC')->get()
        ];
         
            return view('notary_fee.index', $data);  
    }


    public function getInsuranceFeeInfo(Request $request){
        $data = [];

        $inputData = $request->post();

        $notaryFeeData = NotaryFeeSched::getNotaryFeeSched([
            'id' => [$inputData['id']]
        ]);

        if($notaryFeeData){
            $data = [
                'has_error' => 0,
                'data' => $notaryFeeData
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
        // $inputData['monthly_amount'] = str_replace(',', '', $inputData['monthly_amount']);
        // $inputData['annual_amount'] = str_replace(',', '', $inputData['annual_amount']);

        // dd($inputData);

        //check for add function
        if(isset($inputData['id']) && $inputData['id']){
            //update function
            //check first duplicate
            $checkDuplicate = NotaryFeeSched::getNotaryFeeSched([      
                'min_amount' => $inputData['min_amount']
            ]);

            if($checkDuplicate){
                $data = $this->generateNotifications(3);
                return response()->json($data);
            }

            $query = NotaryFeeSched::findOrFail($inputData['id']);
            $result = $query->update($inputData);

            $insFeeSched = NotaryFeeSched::orderBy('min_amount', 'ASC')->get();

            $html = view('insurance_fee.index_data', ['insFeeSched' => $insFeeSched])->render();

            if($result){
                $data = $this->generateNotifications(2);
            }else{
                $data = $this->generateNotifications("");
            }
        }else{
            
            //add function ------------------------------------------------------------------------
            //check first duplicate
            $checkDuplicate = NotaryFeeSched::getNotaryFeeSched([
                'min_amount' => $inputData['min_amount']
            ]);

            if($checkDuplicate){
                $data = $this->generateNotifications(3);
                return response()->json($data);
            }

            // Fetch the updated InsFee list after adding
            // Create a new InsFeeSched
            $result = NotaryFeeSched::create($inputData);
         
             // Fetch the updated department list after adding
            $insFeeSched = NotaryFeeSched::orderBy('min_amount', 'ASC')->get();
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
            'message' => 'An error occurred while trying to add the new Notary Fee. Please try again.',
            'type' => 'error'
        ];

        if($type && $type == 1){
            //add notification success
            $data = [
                'has_error' => 0,
                'title' => 'Success',
                'message' => 'The new Notary Fee Schedulehas been successfully added.',
                'type' => 'success'
            ];
        }

        if($type && $type == 2){
            //update notification success
            $data = [
                'has_error' => 0,
                'title' => 'Update Successful',
                'message' => 'The Notary Fee Schedule have been successfully updated.',
                'type' => 'success'
            ];
        }

        if($type && $type == 3){
            //update notification success
            $data = [
                'has_error' => 1,
                'title' => 'Duplicate Record',
                'message' => 'Notary Fee Schedule already exists.',
                'type' => 'error'
            ];
        }

        return $data;
    }



}