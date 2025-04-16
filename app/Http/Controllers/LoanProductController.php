<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use App\Models\LoanType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class LoanProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loanProducts = LoanProduct::with('loanType')->filter($search)->get();
            $totalRecords = $loanProducts->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loanProducts  // The paginated and formatted data
            ]);

            return response()->json($loanProducts);
        }

        $loan_types = LoanType::all();

        return view('loan.product.index', compact('loan_types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            LoanProduct::create($data);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Duplicate entry'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Loan Product created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(LoanProduct $loanProduct)
    {
        if($loanProduct) {
            return response()->json($loanProduct);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan Product not found'
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoanProduct $loanProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoanProduct $loanProduct)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'status' => 'required',
        ]);

        $loanProduct->name = $request->name;
        $loanProduct->description = $request->description;
        $loanProduct->loan_type_id = $request->loan_type_id;
        $loanProduct->status = $request->status;
        $loanProduct->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan Product updated successfully',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoanProduct $loanProduct)
    {
        $loanProduct->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan Product deleted successfully',
        ]);
    }
}
