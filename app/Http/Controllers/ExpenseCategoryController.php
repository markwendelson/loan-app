<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



//models
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    //
    public function index()
    {
        $categories = ExpenseCategory::paginate(10);

        return view('expenses.category.index', compact(
            'categories'
        ));
    }

    public function create(Request $request)
    {
        if($request->ajax()){
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:expense_categories,name',
                'abbreviation' => 'required|string|unique:expense_categories,abbreviation',
                'description' => 'nullable|string',
            ]);

            if($validator->fails()){
                $allErrors = implode(',', $validator->errors()->all());
                $response = [
                    'status' => 'fail',
                    'message' => 'Validation Failed!: ' . $allErrors,
                ];

                return response()->json($response);
            }

            $data = $request->only([
                'name',
                'abbreviation',
                'description',
                'status'
            ]);

            try {
                ExpenseCategory::create($data);

                return response()->json([
                    'status' => 'success',
                    'message' => 'New Expense Category saved successfully.'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error saving expense category: ' . $e->getMessage()
                ], 500);
            }
        }



    }

    public function update(Request $request)
    {
        if($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:expense_categories,name,'. $request->id .'',
                'abbreviation' => 'required|string|max:255|unique:expense_categories,abbreviation,'. $request->id .'',
                'scope' => 'nullable|string',
                'status' => 'required|in:0,1'
            ]);

            if($validator->fails()){
                $allErrors = implode(',', $validator->errors()->all());
                $response = [
                    'status' => 'fail',
                    'message' => 'Validation Failed!: ' . $allErrors,
                ];

                return response()->json($response);
            }

            $data = $request->only([
                'abbreviation',
                'name',
                'scope',
                'status'
            ]);

            try {
                $docTypeData = ExpenseCategory::find($request->id);

                if(!$docTypeData) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Expense category not found'
                    ], 404);
                }

                $docTypeData->update($data);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Expense category updated successfully.'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error saving expense category: ' . $e->getMessage()
                ], 500);
            }
        }
    }
}
