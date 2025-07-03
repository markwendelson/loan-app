<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Client;
use App\Models\Loan;
use App\Models\LoanFee;
use App\Models\Savings;
use Carbon\Carbon;
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
            $str_balance = getClientBalance($client);
            $balance = (float) str_replace(',', '', $str_balance);

            if($balance<=0 || $request->amount > $balance) {
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

    public function getFees(Request $request)
    {
        $loan = Loan::findOrFail($request->id);

        // adb - open term
        $principal_balance = $this->getPrincipalBalance($loan);
        $result = $this->getNumberOfDays($loan, $request->interest_period ?? '');
        $interest_rate = $loan->interest_rate / 100;
        $interest = ($interest_rate/30) * $result['number_of_days'] * $principal_balance;
        $rounded_interest = round($interest);
        $loan_fee = LoanFee::where('loan_id',$loan->id)->first();

        return response()->json([
            'interest' => number_format($rounded_interest,2),
            'interest_start_date' => $result['start_date'],
            'interest_end_date' => $result['end_date'],
            'monthly_amortization' => number_format($loan_fee->monthly_amortization,2),
            // 12 months
            'insurance_monthly_fee' => $result['number_of_months'] >= 12 ? number_format($loan_fee->insurance_monthly_fee,2) : 0,
            'insurance_annual_fee' => $result['number_of_months'] >= 12 ? number_format($loan_fee->insurance_annual_fee,2) : 0,
            'principal_balance' => number_format($principal_balance,2)
        ]);

    }

    function getPrincipalBalance(Loan $loan)
    {
        $total_principal_payment = Savings::where('loan_id',$loan->id)->sum('principal');
        $principal_balance = $loan->principal - $total_principal_payment;
        return $principal_balance;
    }

    function getNumberOfDays(Loan $loan, string $interest_period = '')
    {

        $savings = Savings::where('loan_id',$loan->id)->where([
                                    ['transaction_type','=','withdraw'],
                                    ['interest_start_date','!=',''],
                                    ['interest_end_date','!=',''],
                                ])->orderBy('transaction_date')->first();
        if($savings) {
            $start_date = $savings->interest_end_date;
        } else {
            $start_date = $loan->disbursed_date;
        }
        $start = Carbon::parse($start_date);
        $current_date = Carbon::now();
        
        $interest_end_date = explode(' ', $interest_period)[1] ?? null;
        if ($interest_end_date) {
            $current_date = Carbon::parse($interest_end_date);
        }


        $number_of_days = (int)$start->diffInDays($current_date);
        $number_of_months = (int)$start->diffInMonths($current_date);

        \Log::info($start);
        \Log::info($current_date);
        \Log::info($number_of_days);
        \Log::info($number_of_months);

        return [
            'start_date' => $start->format('m/d/Y'),
            'end_date' => $current_date->format('m/d/Y'),
            'number_of_days' => $number_of_days,
            'number_of_months' => $number_of_months,
        ];
    }


}
