<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Client;
use App\Models\Loan;
use App\Models\LoanCharge;
use App\Models\LoanProduct;
use App\Models\Savings;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)->get();
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
        $request->merge([
            'applied_amount' => str_replace(',', '', $request->applied_amount),
            'principal' => str_replace(',', '', $request->applied_amount),
            'advance_interest' => str_replace(',', '', $request->advance_interest),
            'service_fee' => str_replace(',', '', $request->service_fee),
            'insurance_fee' => str_replace(',', '', $request->insurance_fee),
            'notary_fee' => str_replace(',', '', $request->notary_fee),
            'loan_no' => Loan::generateLoanNumber()
        ]);

        $data = $request->all();

        try {
            DB::transaction(function () use ($data) {
                $loan = Loan::create($data);

                LoanCharge::create([
                    'loan_id' => $loan->id,
                    'advance_interest' => $data['advance_interest'],
                    'service_fee' => $data['service_fee'],
                    'insurance_fee' => $data['insurance_fee'],
                    'notary_fee' => $data['notary_fee'],
                ]);

            });
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Duplicate entry'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Loan created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        if($loan) {
            $clients = Client::all();
            $loan_products = LoanProduct::active()->get();

            return view('loan.show', compact('loan','clients','loan_products'));
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan Product not found'
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {

        return response()->json([
            'status' => 'success',
            'message' => 'Loan updated successfully',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        DB::table('loan_charges')->where('loan_id',$loan->id)->delete();
        $loan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan deleted successfully',
        ]);
    }

    public function pending(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::pending()->with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)->get();
            $totalRecords = $loans->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.pending');
    }

    public function awaitingDisbursement(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::approved()->with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)->get();
            $totalRecords = $loans->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.awaiting-disbursement');
    }

    public function declined(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::declined()->with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)->get();
            $totalRecords = $loans->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.declined');
    }

    public function withdrawn(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::withdrawn()->with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)->get();
            $totalRecords = $loans->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $loans  // The paginated and formatted data
            ]);
        }

        return view('loan.withdrawn');
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

        return view('loan.closed');
    }

    public function withdrawnLoan(Loan $loan)
    {
        if($loan) {
            $loan->status = LoanStatus::WITHDRAWN;
            $loan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan successfully withdrawn.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found'
        ], 404);
    }

    public function approveLoan(Loan $loan)
    {
        if($loan) {
            $loan->status = LoanStatus::APPROVE;
            $loan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan successfully approved.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found'
        ], 404);
    }

    public function declineLoan(Loan $loan)
    {
        if($loan) {
            $loan->status = LoanStatus::DECLINE;
            $loan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan successfully declined.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found'
        ], 404);
    }

    public function requestApproveLoan(Loan $loan)
    {
        if($loan) {
            $loan->status = LoanStatus::REQUEST_APPROVE;
            $loan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan successfully request for approval.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found'
        ], 404);
    }
}
