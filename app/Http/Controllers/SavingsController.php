<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Client;
use App\Models\Loan;
use App\Models\LoanFee;
use App\Models\Savings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            // 'interest_year' => 'required_if:interest,!=,0',
            // 'insurance_year' => 'required_if:insurance,!=,0'
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

            $interest = $request->interest ?? 0;
            $insurance = $request->insurance ?? 0;
            $principal = $request->principal ?? 0;
            $total_due = $interest + $insurance + $principal;

            if($request->amount < $total_due) {
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

        if (empty($data['insurance'])) {
            unset($data['insurance_month'], $data['insurance_year']);
        }

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

            $transactions = Savings::filter($search)->where('client_id', $client->id)->orderBy('transaction_date', 'DESC')->get();

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
        // $loan_fee = LoanFee::where('loan_id',$loan->id)->first();
        if(Str::slug($loan->loanProduct->loanType->name) == 'loan-with-term') {
            $interest = 0;
            $rounded_interest = 0;
        } else {
            $interest_rate = $loan->interest_rate / 100;
            $interest = ($interest_rate/30) * $result['number_of_days'] * $principal_balance;
            $rounded_interest = round($interest);
        }

        if($loan->is_special) {
            return response()->json([
                'interest' => number_format(0,2),
                'interest_start_date' => $result['start_date'],
                'interest_end_date' => $result['end_date'],
                'monthly_amortization' => number_format($loan->final_monthly_amortization,2),
                // 12 months
                'insurance_monthly_fee' => number_format(0,2),
                'insurance_annual_fee' => number_format(0,2),
                'principal_balance' => number_format($principal_balance,2)
            ]);
        } else {
            return response()->json([
                'interest' => number_format($rounded_interest,2),
                'interest_start_date' => $result['start_date'],
                'interest_end_date' => $result['end_date'],
                'monthly_amortization' => number_format($loan->final_monthly_amortization,2),
                // 12 months
                'insurance_monthly_fee' => $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_monthly * $result['insurance_month'] ,2) : 0,
                'insurance_annual_fee' => $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_yearly,2) : 0,
                'principal_balance' => number_format($principal_balance,2)
            ]);
        }


    }

    function getPrincipalBalance(Loan $loan)
    {
        $total_principal_payment = Savings::where('loan_id',$loan->id)->sum('principal');
        $principal_balance = $loan->final_principal - $total_principal_payment;
        return $principal_balance;
    }

    function getNumberOfDays(Loan $loan, string $interest_end_date = '')
    {

        // interest
        $savings = Savings::where('loan_id',$loan->id)->where([
                                    ['transaction_type','=','withdraw'],
                                    ['interest_start_date','!=',''],
                                    ['interest_end_date','!=',''],
                                ])->orderByDesc('transaction_date')->first();

        $savings_insurance = Savings::where('loan_id',$loan->id)->where([
                                    ['transaction_type','=','withdraw'],
                                    ['insurance_year','!=',''],
                                    ['insurance_month','!=',''],
                                ])->orderByDesc('transaction_date')->first();
        if($savings) {
            $start_date = $savings->interest_end_date;
            $start = Carbon::parse($start_date)->addDay();
            $insurance_start_date =  Carbon::parse($savings->interest_end_date)->addDay();
        } else {
            $start_date = $loan->disbursed_date;
            $start = Carbon::parse($start_date);
            $insurance_start_date = Carbon::parse($loan->disbursed_date);
        }
        $end = Carbon::parse($interest_end_date);

        // interest
        $number_of_days = (int)$start->diffInDays($end);

        // insurance
        $disbursed_date = Carbon::parse($loan->disbursed_date);
        $number_of_months = abs((int)$disbursed_date->diffInMonths($end));
        \Log::info($insurance_start_date);
        \Log::info($end);
        if($number_of_months >= 12) {
            $insurance_month = $this->diffInCalendarMonths($insurance_start_date,$end,$savings_insurance->insurance_year, $savings_insurance->insurance_month);
        }

        \Log::info('start:'.$start);
        \Log::info('end:'.$end);
        \Log::info('number_of_days:'.$number_of_days);
        \Log::info('disbursed_date:'.$disbursed_date);
        \Log::info('number_of_months:'.$number_of_months);
        \Log::info('insurance_month:'.$insurance_month);

        return [
            'start_date' => $start->format('m/d/Y'),
            'end_date' => $end->format('m/d/Y'),
            'number_of_days' => $number_of_days,
            'number_of_months' => $number_of_months,
            'insurance_month' => $insurance_month,
        ];
    }

    function diffInCalendarMonths($start, $end, $insurance_year, $insurance_month) {
         // 1. Normalize inputs
        $start = Carbon::parse($start)->startOfMonth();
        $end = Carbon::parse($end)->startOfMonth();

        // 2. Build full month list between disbursed_date and end_date
        $months = collect();
        $current = $start->copy();
        while ($current <= $end) {
            $months->push($current->format('Y-m'));
            $current->addMonth();
        }

        // 3. Build exclusion list: from disbursed_date to insurance YYYY-MM (inclusive)
        $insurance_cutoff = Carbon::createFromDate($insurance_year, $insurance_month)->startOfMonth();
        \Log::info('insurance_cutoff'.$insurance_cutoff);
        \Log::info('insurance_year'.$insurance_year);
        \Log::info('insurance_month'.$insurance_month);
        $excluded_months = collect();
        $current = $start->copy();
        while ($current <= $insurance_cutoff) {
            $excluded_months->push($current->format('Y-m'));
            $current->addMonth();
        }

        // 4. Exclude those months
        \Log::info($excluded_months);
        $final_months = $months->diff($excluded_months);

        // 5. Return count or array of months if needed
        return $final_months->count(); // or return $final_months if you need the list
    }


}
