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

            $loanProducts = LoanProduct::with('loanType')->filter($search);
            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $loanProducts->count();
            $loanProducts = $loanProducts->skip($start)
                            ->take($length)
                            ->get();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loanProducts  // The paginated and formatted data
            ]);
        }

        $loan_types = LoanType::all();
        $loan_products = LoanProduct::active()->get();
        $repayment_order = ['Principal', 'Interest', 'Fees', 'Penalty'];

        return view('loan.product.index', compact('loan_types','repayment_order','loan_products'));
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
            $loanProduct = LoanProduct::create($data);

            $prerequisiteIds = array_keys($request->input('prerequisites', []));
            $loanProduct->prerequisite()->sync($prerequisiteIds);

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
            $loanProduct->load(['prerequisite:id,name']); // eager load the relationship

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
        $loanProduct->repayment_order = $request->repayment_order;
        $loanProduct->save();

        $prerequisiteIds = array_keys($request->input('prerequisites', []));
        $loanProduct->prerequisite()->sync($prerequisiteIds);

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
        $loanProduct->prerequisite()->detach(); // this loan product's prerequisites
        $loanProduct->prerequisiteOf()->detach(); // other loan products that depend on this
        $loanProduct->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan Product deleted successfully',
        ]);
    }
}
