<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Http\Requests\SavingsRequest;
use App\Models\Attachment;
use App\Models\Client;
use App\Models\Loan;
use App\Models\LoanFee;
use App\Models\LoanProduct;
use App\Models\Savings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

//reference no service
use App\Services\ReferenceNumberService;

class SavingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //get all clients
        $clientData = Client::select(
            'clients.*',
            'client_details.firstname',
            'client_details.middlename',
            'client_details.lastname',
        )
        ->leftJoin('client_details', 'client_details.client_id', '=', 'clients.id')
        ->where('clients.status', 1)
        ->whereNull('clients.deleted_at')
        ->get();

        $clientOptions = '';
        $classificationOptions = '';
        if($clientData->isNotEmpty()) {
            foreach($clientData as $key => $val) {
                $clientOptions .= '<option value="' . $val->id . '">' . ucwords(strtolower($val->firstname)) . ' ' . ucwords(strtolower($val->lastname)) . ' ' . ucfirst(substr($val->middlename, 0, 1)) . '.</option>';
            }

            foreach(config('constants.savings_classifications') as $key => $val) {
                $classificationOptions .= '<option value="' . $val . '">' . $val . '</option>';
            }
        }

        if($request->ajax()) {
            $search = $request->search['value'];

            if (!auth()->user()->hasRole('admin|super_admin')) {
                $savings = Client::where('branch_id', auth()->user()->current_branch_id)
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

        return view('savings.index', compact(
            'clientData',
            'clientOptions',
            'classificationOptions',
        ));
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
    public function store(SavingsRequest $request)
    {

        try {

            DB::beginTransaction();
            // check balance if withdraw
            $client = Client::findOrFail($request->client_id);

            $str_balance = getClientBalance($client);
            $client_balance = (float) str_replace(',', '', $str_balance);
            $amount = (float) str_replace(',', '', $request->amount);

            if($request->transaction_type == 'withdraw') {
                $loan_ids = $request->loan_id;
                if (!is_array($loan_ids)) {
                    $loan_ids = $loan_ids ? [$loan_ids] : [];
                }

                if(empty($loan_ids)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Select loan',
                    ], 500);
                }

                $transaction_code = $request->transaction_code_select == null ? null : implode(",",$request->transaction_code_select);

                Log::info('client balance: '.$client_balance);
                Log::info('amount: ' . $amount);
                Log::info($client_balance<=0 || $amount > $client_balance);
                if($client_balance<=0 || $amount > $client_balance) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient Balance',
                    ], 500);
                }

                // verify amount thru mode of payment
                if ($transaction_code) {
                    $sumAmount = Savings::where('client_id', $request->client_id)
                                    ->whereIn('transaction_code', explode(',', $transaction_code))
                                    ->where('mode_of_payment', $request->mode_of_payment)
                                    ->where('transaction_type', 'deposit')
                                    ->sum('amount');

                    if (abs((float)$sumAmount - $amount) > 0.01) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Invalid amount.',
                        ], 500);
                    }
                }

                $total_requested_amount = 0;
                $transactions_to_create = [];
                $total_loans = count($loan_ids);
                $current_index = 0;

                $due_change = isset($request->due_change) ? (float)str_replace(',', '', $request->due_change) : 0;

                foreach ($loan_ids as $loan_id) {
                    $current_index++;
                    $interest = isset($request->interest[$loan_id]) ? (float)str_replace(',', '', $request->interest[$loan_id]) : 0;
                    $insurance = isset($request->insurance[$loan_id]) ? (float)str_replace(',', '', $request->insurance[$loan_id]) : 0;
                    $principal = isset($request->principal[$loan_id]) ? (float)str_replace(',', '', $request->principal[$loan_id]) : 0;
                    $principal_advance = isset($request->principal_advance[$loan_id]) ? (float)str_replace(',', '', $request->principal_advance[$loan_id]) : 0;
                    $membership_fee = isset($request->membership_fee[$loan_id]) ? (float)str_replace(',', '', $request->membership_fee[$loan_id]) : 0;
                    $allocated_amount = isset($request->loan_amount[$loan_id]) ? (float)str_replace(',', '', $request->loan_amount[$loan_id]) : 0;

                    $exclude_interest = isset($request->exclude_interest[$loan_id]);
                    $interest_start_date = $exclude_interest ? null : ($request->interest_start_date[$loan_id] ?? null);
                    $interest_end_date = $exclude_interest ? null : ($request->interest_end_date[$loan_id] ?? null);

                    $total_due = ($exclude_interest ? 0 : $interest) + $insurance + $principal + $principal_advance + $membership_fee;

                    if($allocated_amount < $total_due) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Insufficient amount for selected loan(s).',
                        ], 500);
                    }

                    $loan = Loan::where('id', $loan_id)->first();
                    $loan_balance = getLoanBalance($loan);

                    $is_early_settlement = isset($request->is_early_settlement[$loan_id]);

                    if (!$loan->is_special && $is_early_settlement) {
                        $principal_submitted = $principal;
                        $regular_principal = $loan_balance;
                        $special_principal_total = $principal - $loan_balance;

                        $principal = $regular_principal;
                        $principal_balance = 0;

                        if ($special_principal_total > 0) {
                            $special_loans = Loan::specialLoan($client->id)->get();
                            foreach ($special_loans as $special_loan) {
                                $spec_loan_balance = $this->getPrincipalBalance($special_loan);
                                $spec_allocated_principal = min($special_principal_total, $spec_loan_balance);
                                $special_principal_total -= $spec_allocated_principal;

                                if ($spec_allocated_principal > 0) {
                                    $spec_tx = $request->except(['loan_id', 'interest', 'interest_deficit', 'insurance', 'principal', 'principal_advance', 'membership_fee', 'interest_start_date', 'interest_end_date', 'amount', 'insurance_month', 'insurance_year', 'due_change']);
                                    $spec_tx['loan_id'] = $special_loan->id;
                                    $spec_tx['interest'] = 0;
                                    $spec_tx['interest_deficit'] = 0;
                                    $spec_tx['interest_start_date'] = null;
                                    $spec_tx['interest_end_date'] = null;
                                    $spec_tx['insurance'] = 0;
                                    $spec_tx['principal'] = $spec_allocated_principal;
                                    $spec_tx['principal_advance'] = 0;
                                    $spec_tx['membership_fee'] = 0;
                                    $spec_tx['amount'] = $spec_allocated_principal;
                                    $spec_tx['principal_balance'] = $spec_loan_balance - $spec_allocated_principal;
                                    $spec_tx['transaction_code'] = $transaction_code;
                                    $transactions_to_create[] = $spec_tx;
                                    $total_requested_amount += $spec_allocated_principal;
                                }
                            }
                        }

                        $allocated_amount = $allocated_amount - ($principal_submitted - $regular_principal);
                    } else {
                        $principal_balance = $loan_balance - $principal;
                    }

                    if (!$loan->is_special && !$is_early_settlement) {
                        $allocated_amount = $allocated_amount + $due_change;
                    }

                    $total_requested_amount += $allocated_amount;

                    if ($total_loans == 1) {
                        $allocated_amount = $amount;
                    }

                    Log::info('loan_id:'.$loan_id);
                    Log::info('allocated_amount:'.$allocated_amount);

                    $interest_deficit = isset($request->interest_deficit[$loan_id]) ? (float)str_replace(',', '', $request->interest_deficit[$loan_id]) : 0;

                    $tx_data = $request->except(['loan_id', 'interest', 'interest_deficit', 'insurance', 'principal', 'principal_advance', 'membership_fee', 'interest_start_date', 'interest_end_date', 'amount', 'insurance_month', 'insurance_year', 'due_change']);
                    $tx_data['loan_id'] = $loan_id;
                    $tx_data['interest'] = $exclude_interest ? 0 : $interest;
                    $tx_data['interest_deficit'] = $interest_deficit;
                    $tx_data['interest_start_date'] = $interest_start_date;
                    $tx_data['interest_end_date'] = $interest_end_date;
                    $tx_data['insurance'] = $insurance;
                    $tx_data['principal'] = $principal;
                    $tx_data['principal_advance'] = $principal_advance;
                    $tx_data['membership_fee'] = $membership_fee;
                    $tx_data['amount'] = $allocated_amount;
                    $tx_data['principal_balance'] = $principal_balance;
                    $tx_data['transaction_code'] = $transaction_code;

                    if (isset($request->insurance_month[$loan_id])) {
                        $tx_data['insurance_month'] = $request->insurance_month[$loan_id];
                    }
                    if (isset($request->insurance_year[$loan_id])) {
                        $tx_data['insurance_year'] = $request->insurance_year[$loan_id];
                    }

                    $transactions_to_create[] = $tx_data;
                }

                if (abs($total_requested_amount - $amount) > 0.01) {
                    Log::info( $total_requested_amount."|".$due_change."|".$amount);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The sum of allocated loan payments and change must equal the total transaction amount.',
                    ], 500);
                }

                $first_savings = null;
                foreach ($transactions_to_create as $index => $tx_data) {
                    $tx_data['due_change'] = $index === 0 ? $due_change : 0;
                    $tx_data['transaction_no'] = Savings::generateTransactionNumber($request->transaction_date, 'withdraw');
                    $savings = Savings::create($tx_data);
                    if (!$first_savings) {
                        $first_savings = $savings;
                    }
                }
                $savings = $first_savings;

            } else {
                $data = $request->except([
                    'loan_id', 'interest', 'interest_deficit', 'insurance', 'insurance_month', 'insurance_year',
                    'principal', 'principal_advance', 'membership_fee', 'loan_amount',
                    'interest_start_date', 'interest_end_date', 'exclude_interest',
                    'is_early_settlement', 'include_special_loan_balance'
                ]);
                $data['transaction_no'] = Savings::generateTransactionNumber($request->transaction_date, 'deposit');

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

                //insert reference_no
                $referenceNumberService = new ReferenceNumberService();

                $referenceNumber = $referenceNumberService->generateReferenceNumber(Savings::class, auth()->user()->current_branch_id);
                $data['reference_no'] = $referenceNumber . 'S';

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

            if($request->transaction_type == 'deposit') {
                $remaining_balance = $client_balance + $amount;
            }

            if($request->transaction_type == 'withdraw') {
                $remaining_balance = $client_balance - $amount;
            }

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

            $start_date = Carbon::createFromFormat('m/d/Y', $result['start_date'])->startOfDay();
            $end_date = Carbon::createFromFormat('m/d/Y', $result['end_date'])->startOfDay();

            if ($start_date->gte($end_date)) {
                $interest = 0;
                $rounded_interest = 0;
            } else {
                $interest = ($interest_rate/30) * $result['number_of_days'] * $principal_balance;
                $rounded_interest = round($interest - ($savings->interest_deficit ?? 0));
            }
            $insurance_monthly_fee = $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_monthly * $result['insurance_month'] ,2) : 0;
            $insurance_annual_fee = $result['number_of_months'] >= 12 ? number_format($loan->final_insurance_yearly,2) : 0;
        }

        $membership_fee_balance = getMembershipFeeBalance($loan);
        $total_principal_advance_payment = Savings::where('loan_id', $loan->id)->sum('principal_advance');
        $principal_advance_balance = ($loan->loanCharges->principal_advance ?? 0) - $total_principal_advance_payment;

        $loan_product = LoanProduct::whereId($loan->loan_product_id)->first('repayment_order');

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
                'principal_advance_balance' => number_format($principal_advance_balance, 2),
                'previous_interest_deficit' => number_format($savings->interest_deficit ?? 0,2),
                'current_interest' => number_format(0,2),
                'repayment_order' => $loan_product->repayment_order,
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
                'principal_advance_balance' => number_format($principal_advance_balance, 2),
                'previous_interest_deficit' => number_format($savings->interest_deficit ?? 0,2),
                'current_interest' => number_format($interest,2),
                'repayment_order' => $loan_product->repayment_order,
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
        $loan_type = Str::slug($loan->loanProduct->loanType->name);

        $principal_balance = $loan->final_principal
            - $total_principal_payment
            - (
                in_array($loan_type, ['loan-with-term', 'auto-debit', 'mortgage-loan'])
                    ? $loan->loanCharges->advance_interest
                    : 0
            );

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

        $savings = Savings::where('loan_id',$loan->id)
                            ->where([
                                ['transaction_type','=','withdraw'],
                                ['interest','>',0],
                            ])
                            ->whereNotNull('interest_end_date')
                            ->orderByDesc('interest_end_date')
                            ->first();

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

    public function saveMultipleEntries(Request $request)
    {
        $inputData = $request->all();

        $response = []; //handles response data
        $processedData = [];

        if(isset($inputData) && $inputData) {
            foreach($inputData as $key => $val) {
                $referenceNumberService = new ReferenceNumberService();

                $referenceNumber = $referenceNumberService->generateReferenceNumber(Savings::class, auth()->user()->current_branch_id);

                $processedData[] = [
                    'client_id' => $val['client_id'],
                    'reference_no' => $referenceNumber . 'S',
                    'transaction_no' => Savings::generateTransactionNumber($val['date'], 'deposit'),
                    'transaction_date' => $val['date'],
                    'description' => $val['description'],
                    'transaction_type' => 'deposit',
                    'mode_of_payment' => $val['transaction_type'],
                    'transaction_code' => $val['transaction_code'],
                    'amount' => $val['amount_withdrawn'],
                    'classifications' => $val['classifications'],
                    'atm_balance' => $val['atm_balance'] ?? 0,
                ];
            }

            //bulk insert to savings
            $result = Savings::insert($processedData);

            if($result) {
                $response = [
                    'status' => 'success',
                    'message' => 'Savings data successfully saved!'
                ];
            }
        }else{
            $response = [
                'status' => 'error',
                'message' => 'An error occured! Please try again later.'
            ];
        }

        return response()->json($response);
    }

    public function createTransaction(int $client_id)
    {
        $client = Client::findOrFail($client_id);
        $loans = $client->getApproveLoans()->whereNotNull('disbursed_date');

        $balance = getClientBalance($client);


        return view('savings.create-transaction', compact('client', 'loans'));
    }

}
