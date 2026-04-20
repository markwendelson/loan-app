<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Attachment;
use App\Models\Client;
use App\Models\Loan;
use App\Models\LoanFee;
use App\Models\Savings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

            if (!auth()->user()->hasRole('admin|superadmin')) {
                $savings = Client::where('branch_id', auth()->user()->branch_id)
                    ->with('details','savings')
                    ->filter($search);
            } else {
                $savings = Client::with('details','savings')->filter($search);
            }
            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $savings->count();
            $savings = $savings->skip($start)
            ->take($length)
            ->get();

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
            'attachment' => 'required_if:mode_of_change,authorized_person|file|mimes:jpg,jpeg,png,pdf|max:2048'
            // 'interest_year' => 'required_if:interest,!=,0',
            // 'insurance_year' => 'required_if:insurance,!=,0'
        ]);
        try {

            DB::beginTransaction();
            // check balance if withdraw
            $client = Client::findOrFail($request->client_id);

            if($request->transaction_type == 'withdraw') {
                $request->merge([
                    'transaction_code' => $request->transaction_code_select == null ? null : implode(",",$request->transaction_code_select),
                    'interest' => $request->exclude_interest ? 0 : $request->interest,
                    'interest_start_date' => $request->exclude_interest ? null : $request->interest_start_date,
                    'interest_end_date' => $request->exclude_interest ? null : $request->interest_end_date,
                ]);
                $str_balance = getClientBalance($client);
                $client_balance = (float) str_replace(',', '', $str_balance);

                if($client_balance<=0 || $request->amount > $client_balance) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient Balance',
                    ], 500);
                }

                 if(!($request->loan_id)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Select loan',
                    ], 500);
                }

                // if($request->amount < $request->principal) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Insufficient amount.',
                //     ],500);
                // }

                $interest = $request->interest ?? 0;
                $insurance = $request->insurance ?? 0;
                $principal = $request->principal ?? 0;
                $membership_fee = $request->membership_fee ?? 0;
                $total_due = $interest + $insurance + $principal + $membership_fee;
                $loan = Loan::where('id',$request->loan_id)->first();
                $loan_balance = getLoanBalance($loan);
                $principal_balance = $loan_balance - $principal;

                $request->merge([
                        'principal_balance' => $principal_balance,
                    ]
                );

                if($request->amount < $total_due) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient amount.',
                    ], 500);
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

            // check if duplicate
            if($request->transaction_code != null) {
                $exists = Savings::where([
                    'client_id' => $request->client_id,
                    'transaction_code' => $request->transaction_code,
                    'mode_of_payment' => $request->mode_of_payment,
                    'transaction_type' => $request->transaction_type
                ])->exists();

                if($exists) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Transaction code already exists.',
                    ], 500);
                }
            }


            if(!empty($data['is_early_settlement'])) {
                $loans = $client->getApproveLoans();

                foreach ($loans as $loan) {
                    $payments = $loan?->payments->where('transaction_type', 'withdraw');

                    $principal_balance = $loan->final_principal;
                    $principal_payments = $payments->sum('principal');
                    $earyl_settlement_amount = $principal_balance - $principal_payments;

                    $data['amount'] = $earyl_settlement_amount;
                    $data['principal'] = $earyl_settlement_amount;
                    $data['loan_id'] = $loan->id;

                    if($loan->is_special) {
                        $data['interest'] = 0;
                        $data['insurance'] = 0;
                    }

                    Log::info('principal'.$loan->final_principal);
                    Log::info('payment:'.$payments->sum('principal'));
                    Log::info('earyl_settlement_amount'.$earyl_settlement_amount);
                    Log::info('----------------------------------------------------------');

                    $savings = Savings::create($data);

                }

            } else {
                $savings = Savings::create($data);
            }

            if ($request->hasFile('attachment')) {

                $file = $request->file('attachment');
                $filename = $file->getClientOriginalName();
                $folder = 'attachments/' . $savings->transaction_no;
                $path = $file->storeAs($folder, $filename, 'public');

                Attachment::create([
                    'file_reference' => 'savings',
                    'transaction_no' => $savings->transaction_no,
                    'file_name'      => $filename,
                    'file_path'      => $path,
                ]);
            }

            $remaining_balance = $client_balance - $request->amount;

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction created successfully',
                'balance' => number_format($remaining_balance,2)
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }

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
        if($saving->transaction_type == 'withdraw' && $saving->loan->status == LoanStatus::CLOSED->value) {
            Log::info('updating: '.$saving->loan->loan_no);
            Loan::where([
                'loan_no' => $saving->loan->loan_no,
            ])->update([
                'status' => LoanStatus::APPROVE,
                'closed_date' => null,
            ]);

            Loan::where([
                'is_additional_loan' => 1,
                'reference_loan_no' => $saving->loan->loan_no,
            ])->update([
                'status' => LoanStatus::APPROVE,
                'closed_date' => null,
            ]);

            // return response()->json([
            //     'status' => 'failed',
            //     'message' => 'Transaction cannot be deleted',
            // ],422);
        }

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

            $transactions = Savings::withTrashed()->filter($search)->where('client_id', $client->id)->orderBy('transaction_date', 'DESC')->orderBy('id','DESC');

            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $transactions->count();
            $transactions = $transactions->skip($start)
            ->take($length)
            ->get();

            $data = $transactions->map(function($saving) {
                $saving['amount'] = number_format($saving['amount'],2);
                $saving['mop_trans_code'] = '<span>'.str_replace("_"," ",$saving->mode_of_payment).'</span><br><span>'.$saving->transaction_code.'</span>';
                return $saving;
            });

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
        $savings = Savings::where('loan_id',$loan->id)->orderBy('interest_end_date', 'DESC')->limit(1)->first();

        // adb - open term
        $principal_balance = $this->getPrincipalBalance($loan);
        $special_loan_balance = getSpecialLoanBalance($loan->borrower_id);
        // $loan_fee = LoanFee::where('loan_id',$loan->id)->first();
        $result = array();
        if(Str::slug($loan->loanProduct->loanType->name) == 'loan-with-term') {
            $result = $this->getInsuranceFee($loan, $request->transaction_date);
            $interest = 0;
            $rounded_interest = 0;
            $insurance_monthly_fee = $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_monthly * $result['insurance_month'] ,2) : 0;
            $insurance_annual_fee = $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_yearly,2) : 0;
        } else {
            $result = $this->getNumberOfDays($loan, $request->interest_period ?? '');
            $interest_rate = $loan->interest_rate / 100;
            $interest = ($interest_rate/30) * $result['number_of_days'] * $principal_balance;
            $rounded_interest = round($interest - ($savings->interest_deficit ?? 0));
            $insurance_monthly_fee = $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_monthly * $result['insurance_month'] ,2) : 0;
            $insurance_annual_fee = $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_yearly,2) : 0;
        }

        $membership_fee_balance = getMembershipFeeBalance($loan);

        if($loan->is_special) {
            return response()->json([
                'interest' => number_format(0,2),
                'interest_start_date' => $result['start_date'],
                'interest_end_date' => $result['end_date'],
                'monthly_amortization' => number_format($loan->final_monthly_amortization,2),
                // 12 months
                'insurance_monthly_fee' => number_format(0,2),
                'insurance_annual_fee' => number_format(0,2),
                'principal_balance' => number_format($principal_balance,2),
                'special_loan_balance' => '0',
                'early_settlement_amount' => number_format($principal_balance,2),
                'membership_fee_balance' => $membership_fee_balance,
                'previous_interest_deficit' => number_format($savings->interest_deficit,2) ?? 0,
                'current_interest' => number_format(0,2),
            ]);
        } else {
            return response()->json([
                'interest' => number_format($rounded_interest,2),
                'interest_start_date' => $result['start_date'],
                'interest_end_date' => $result['end_date'],
                'monthly_amortization' => number_format($loan->final_monthly_amortization,2),
                // 12 months
                'insurance_monthly_fee' => $insurance_monthly_fee,
                'insurance_annual_fee' => $insurance_annual_fee,
                'principal_balance' => number_format($principal_balance,2),
                'special_loan_balance' => number_format($special_loan_balance,2),
                'early_settlement_amount' => number_format($principal_balance + $special_loan_balance,2),
                'membership_fee_balance' => $membership_fee_balance,
                'previous_interest_deficit' => number_format($savings->interest_deficit ?? 0,2),
                'current_interest' => number_format($interest,2),
            ]);
        }


    }

    public function getFeesByMonthAndYear(Request $request)
    {
        $loan = Loan::findOrFail($request->id);
        $date = Carbon::create($request->insurance_year,$request->insurance_month,1);
        // adb - open term
        $result = array();
        if(Str::slug($loan->loanProduct->loanType->name) == 'loan-with-term') {
            $result = $this->getInsuranceFee($loan, $date);
            $insurance_monthly_fee = $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_monthly * $result['insurance_month'] ,2) : 0;
            $insurance_annual_fee = $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_yearly,2) : 0;
        } else {
            $result = $this->getNumberOfDays($loan);
            $insurance_monthly_fee = $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_monthly * $result['insurance_month'] ,2) : 0;
            $insurance_annual_fee = $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_yearly,2) : 0;
        }


        return response()->json([
            'insurance_monthly_fee' => $insurance_monthly_fee,
            'insurance_annual_fee' => $insurance_annual_fee,
        ]);
    }

    function getPrincipalBalance(Loan $loan)
    {
        $total_principal_payment = Savings::where('loan_id',$loan->id)->sum('principal');
        $principal_balance = $loan->final_principal - $total_principal_payment;
        return $principal_balance;
    }

    function getInsuranceFee(Loan $loan, string $transaction_date)
    {
        $savings_insurance = Savings::where('loan_id',$loan->id)->where([
            ['transaction_type','=','withdraw'],
            ['insurance_year','!=',''],
            ['insurance_month','!=',''],
        ])->orderByDesc('transaction_date')->first();

        $start = Carbon::parse($loan->disbursed_date);

        if($savings_insurance) {
            $month = $savings_insurance->insurance_month;
            $year = $savings_insurance->insurance_year;
            $insurance_start_date = Carbon::createFromDate($year, $month, 1)->addMonth();
        } else {
            $insurance_start_date = Carbon::parse($loan->disbursed_date);
        }
        $end = Carbon::parse($transaction_date);
        $disbursed_date = Carbon::parse($loan->disbursed_date);
        $number_of_months = abs((int)$disbursed_date->diffInMonths($end));
        $insurance_month = 0;
        if($number_of_months >= 12) {
            $insurance_month = $this->diffInCalendarMonths($insurance_start_date,$end,$savings_insurance?->insurance_year, $savings_insurance?->insurance_month);
        }

        \Log::info('start:'.$start);
        \Log::info('end:'.$end);
        \Log::info('disbursed_date:'.$disbursed_date);
        \Log::info('number_of_months:'.$number_of_months);
        \Log::info('insurance_month:'.$insurance_month ?? 0);

        return [
            'start_date' => $start->format('m/d/Y'),
            'end_date' => $end->format('m/d/Y'),
            'number_of_months' => $number_of_months,
            'insurance_month' => $insurance_month ?? 0,
        ];
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
        \Log::info('insurance_start_date: '.$insurance_start_date);
        $insurance_month = 0;
        if($number_of_months >= 12) {
            $insurance_month = $this->diffInCalendarMonths($insurance_start_date,$end,$savings_insurance?->insurance_year, $savings_insurance?->insurance_month);
        }

        \Log::info('start:'.$start);
        \Log::info('end:'.$end);
        \Log::info('number_of_days:'.$number_of_days);
        \Log::info('disbursed_date:'.$disbursed_date);
        \Log::info('number_of_months:'.$number_of_months);
        \Log::info('insurance_month:'.$insurance_month ?? 0);

        return [
            'start_date' => $start->format('m/d/Y'),
            'end_date' => $end->format('m/d/Y'),
            'number_of_days' => $number_of_days,
            'number_of_months' => $number_of_months,
            'insurance_month' => $insurance_month ?? 0,
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
        // If no exclusions, return full count
        if (empty($insurance_year) || empty($insurance_month)) {
            return $months->count() - 12;
        }


        // 3. Build exclusion list: from disbursed_date to insurance YYYY-MM (inclusive)
        $insurance_cutoff = Carbon::createFromDate($insurance_year, $insurance_month)->startOfMonth();
        \Log::info('start:'.$start);
        \Log::info('insurance_cutoff:'.$insurance_cutoff);
        \Log::info('insurance_year:'.$insurance_year);
        \Log::info('insurance_month:'.$insurance_month);
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

    public function printReceipt($id)
    {
        $savings = Savings::with('client.details','client.branch')->findOrFail($id);

        // Get branch name from client's branch
        $branchName = $savings->client->branch ? $savings->client->branch->name : 'Unknown';

        // Custom title rules
        if (strcasecmp($branchName, 'Guiuan') === 0) {
            $branchTitle = 'Head Office';
        } else {
            $branchTitle = $branchName . ' Branch';
        }

        return view('savings.receipt', compact('savings', 'branchTitle'));
    }

    public function updateTransactionCode(Request $request, int $id)
    {
        $savings = Savings::where('id', $id)->first();
        $savings->transaction_code = $request->transaction_code;
        $savings->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction code updated successfully',
        ]);
    }

}
