<?php

namespace App\Http\Controllers;

use App\Models\LoanType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class LoanTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loanTypes = LoanType::filter($search)->get();
            $totalRecords = $loanTypes->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loanTypes  // The paginated and formatted data
            ]);

            return response()->json($loanTypes);
        }

        return view('loan.type.index');
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
            LoanType::create($data);
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
            'message' => 'Loan Type created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(LoanType $loanType)
    {
        if($loanType) {
            return response()->json($loanType);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan Type not found'
        ], 404);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoanType $loanType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoanType $loanType)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'status' => 'required',
        ]);

        $loanType->name = $request->name;
        $loanType->description = $request->description;
        $loanType->status = $request->status;
        $loanType->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan Type updated successfully',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoanType $loanType)
    {
        $loanType->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan Type deleted successfully',
        ]);
    }
}
