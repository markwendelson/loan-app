<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Client;
use App\Models\Loan;
use App\Models\Savings;
use Illuminate\Http\Request;

class SavingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $savings = Client::with('details','savings')->filter($search)->get();
            $totalRecords = $savings->count();

            $data = $savings->map(function($saving) {
                $balance = $saving->savings
                                ->where('transaction_type', 'deposit')->sum('amount')
                                - $saving->savings
                                ->where('transaction_type', 'withdraw')->sum('amount');

                $saving['balance'] = number_format($balance,2);

                $saving['fullname'] = $saving->details?->fullname;
                return $saving;
            });

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $data  // The paginated and formatted data
            ]);
        }

        return view('savings.index');
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

        $request->validate([
            'amount' => 'required',
            'interest_year' => 'required_if:interest,!=,0',
            'insurance_year' => 'required_if:insurance,!=,0'
        ]);

        // check balance if withdraw
        $client = Client::findOrFail($request->client_id);

        if($request->transaction_type == 'withdraw') {
            $balance = getClientBalance($client);
            if($balance<=0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient Balance',
                ],500);
            }

             if(!($request->loan_id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Select loan',
                ],500);
            }

            if($request->amount < $request->principal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient amount.',
                ],500);
            }
        }

        $data = $request->all();

        // $loan = Loan::where([
        //     'borrower_id' => $request->client_id,
        //     'status' => LoanStatus::APPROVE
        // ])->first();

        // $data['loan_id'] = $loan->id;

        Savings::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction created successfully',
            'balance' => getClientBalance($client)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Savings $saving)
    {
        if($saving) {
            return response()->json($saving);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Transaction not found'
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Savings $savings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Savings $saving)
    {
        $saving->transaction_date = $request->transaction_date;
        $saving->transaction_type = $request->transaction_type;
        $saving->amount = $request->amount;
        $saving->description = $request->description;
        $saving->notes = $request->notes;
        $saving->classifications = $request->classifications;
        $saving->save();

        $client = Client::findOrFail($saving->client_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction updated successfully',
            'balance' => getClientBalance($client)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Savings $saving)
    {
        $saving->delete();
        $client = Client::findOrFail($saving->client_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction deleted successfully',
            'balance' => getClientBalance($client)
        ]);
    }

    public function transactions(Request $request, int $client_id)
    {
        $client = Client::findOrFail($client_id);
        $balance = getClientBalance($client);

        $client['balance'] = $balance;

        if($request->ajax()) {
            $search = $request->search['value'];

            $transactions = Savings::filter($search)->where('client_id', $client->id)->get();

            $data = $transactions->map(function($saving) {
                $saving['amount'] = number_format($saving['amount'],2);
                return $saving;
            });

            $totalRecords = $transactions->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $data  // The paginated and formatted data
            ]);
        }

        $loans = $client->getApproveLoans();

        return view('savings.transactions', compact('client','loans'));
    }

    
}
