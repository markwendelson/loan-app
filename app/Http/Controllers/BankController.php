<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

//models
use App\Models\Bank;

class BankController extends Controller
{
    public function index()
    {
        return view('bank.index');
    }

    public function store(Request $request)
    {
        $response = [];

        try {

            $inputData = $request->except('id');

            $validator = Validator::make($request->all(), [
                'bank_code' => 'required|unique:bank,name,',
                'name' => 'required|string|max:255|unique:bank,name,',
            ]);

            if($validator->fails()){
                $allErrors = implode(',', $validator->errors()->all());
                $response = [
                    'status' => 'error',
                    'message' => 'Validation Failed!: ' . $allErrors,
                ];

                return response()->json($response);
            }

            DB::beginTransaction();

            Bank::create($inputData);

            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Bank account created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollback();
        }

        return response()->json($response);
    }

    public function getBankData($id)
    {
        $response = [];

        $bank = Bank::find($id);

        if($bank){
            $response = [
                'status' => 'success',
                'message' => 'Bank data retrieved successfully.',
                'data' => $bank->toArray()
            ];
        }else{
            $response = [
                'status' => 'error',
                'message' => 'Bank data not found.'
            ];
        }

        return response()->json($response);
    }

    public function put(Request $request)
    {
        $response = [];

        try {
            DB::beginTransaction();

            $inputData = $request->all();

            $validator = Validator::make($request->all(), [
                'bank_code' => 'required|unique:bank,name,' . $inputData['id'],
                'name' => 'required|string|max:255|unique:bank,name,' . $inputData['id'],
            ]);

            if($validator->fails()){
                $allErrors = implode(',', $validator->errors()->all());
                $response = [
                    'status' => 'error',
                    'message' => 'Validation Failed!: ' . $allErrors,
                ];

                return response()->json($response);
            }

            $bank = Bank::find($inputData['id']);

            $bank->update($inputData);

            $response = [
                'status' => 'success',
                'message' => 'Bank account updated successfully.'
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            $response = [
                'status' => 'error',
                'message' => 'An error occurred while updating the bank account. Please try again later.',
            ];
        }

        return response()->json($response);
    }

    public function delete($id)
    {
        $response = [];

        try {
            DB::beginTransaction();

            Bank::destroy($id);

            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Bank account deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollback();

            $response = [
                'status' => 'error',
                'message' => 'An error occurred while deleting the bank account. Please try again later.',
            ];
        }

        return response()->json($response);
    }
}
