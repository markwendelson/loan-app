<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Loan;
use App\Models\LoanProduct;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::with('loanProduct')->filter($search)->get();
            $totalRecords = $loans->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all();
        $loan_products = LoanProduct::active()->get();

        return view('loan.create', compact('loan_products', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            Loan::create($data);
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
    public function show(Loan $loanProduct)
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
    public function edit(Loan $loanProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loanProduct)
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
    public function destroy(Loan $loanProduct)
    {
        $loanProduct->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan Product deleted successfully',
        ]);
    }

    public function pending(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::with('loanProduct')->filter($search)->get();
            $totalRecords = $loans->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        $loan_products = LoanProduct::active()->get();

        return view('loan.pending', compact('loan_products'));
    }

    public function awaitingDisbursement(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::with('loanProduct')->filter($search)->get();
            $totalRecords = $loans->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        $loan_products = LoanProduct::active()->get();

        return view('loan.awaiting-disbursement', compact('loan_products'));
    }

    public function declined(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::with('loanProduct')->filter($search)->get();
            $totalRecords = $loans->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        $loan_products = LoanProduct::active()->get();

        return view('loan.declined', compact('loan_products'));
    }

    public function withdrawn(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::with('loanProduct')->filter($search)->get();
            $totalRecords = $loans->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        $loan_products = LoanProduct::active()->get();

        return view('loan.withdrawn', compact('loan_products'));
    }

    public function closed(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::with('loanProduct')->filter($search)->get();
            $totalRecords = $loans->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        $loan_products = LoanProduct::active()->get();

        return view('loan.closed', compact('loan_products'));
    }
}
