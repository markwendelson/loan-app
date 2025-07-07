<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Http\Requests\LoanRequest;
use App\Models\Client;
use App\Models\InsuranceFeeSchedule;
use App\Models\Loan;
use App\Models\LoanCharge;
use App\Models\LoanFee;
use App\Models\LoanProduct;
use App\Models\Savings;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $loans = Loan::with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)
                        ->orderByDesc('loan_date')
                        ->orderByDesc('loan_no')
                        ->get();

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
    public function create(Request $request)
    {
        $previous_loan = Loan::with('borrower.details','payments','loanCharges')->where('loan_no', $request->loan_no_reference)->first();
        if($previous_loan) {
            $reloan_date = date('Y-m-d');
            $reloan_deduction = $this->calculateReloanDeduction($previous_loan, $reloan_date);
        }

        $clients = Client::active()->get();
        $loan_products = LoanProduct::active()->get();

        return view('loan.create', compact('loan_products', 'clients','reloan_deduction','previous_loan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LoanRequest $request)
    {
        $data = $request->all();

        if($data['reloan']) {
            return $this->processReloan($data);
        }

        try {

            $loan_product = LoanProduct::with('prerequisite')->find($data['loan_product_id']);
            if ($loan_product->prerequisite->isNotEmpty()) {
                $data['is_special'] = 1;

                // must have principal loan
                $principal_loan = Loan::where('borrower_id', $data['borrower_id'])
                    ->where('status', LoanStatus::APPROVE)
                    ->where('is_special', 0)
                    ->first();

                if (!in_array($principal_loan->loan_product_id,$loan_product->prerequisite->pluck('id')->toArray())) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Required prerequisite loan is missing.',
                    ], 400);
                }

                $alreadyHasLoan = Loan::where([
                                'borrower_id' => $data['borrower_id'],
                                'is_special' => 1
                            ])
                            ->where('status', LoanStatus::APPROVE)
                            ->orWhere('status', LoanStatus::REQUEST_APPROVE)
                            ->orWhere('status', LoanStatus::PENDING)
                            ->first();

                if($alreadyHasLoan) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Client already has a pending or approved loan']
                        , 400);
                }

            } else {
                $data['is_special'] = 0;

                $alreadyHasLoan = Loan::where([
                                'borrower_id' => $data['borrower_id'],
                            ])
                            ->where('status', LoanStatus::APPROVE)
                            ->orWhere('status', LoanStatus::REQUEST_APPROVE)
                            ->orWhere('status', LoanStatus::PENDING)
                            ->first();

                if($alreadyHasLoan) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Client already has a pending or approved loan']
                        , 400);
                }

            }

            DB::transaction(function () use ($data) {

                $loan = Loan::create($data);

                LoanCharge::create([
                    'loan_id' => $loan->id,
                    'advance_interest' => $data['advance_interest'],
                    'service_fee' => $data['service_fee'],
                    'insurance_fee' => $data['insurance_fee'],
                    'notary_fee' => $data['notary_fee'],
                ]);

                $insurance = InsuranceFeeSchedule::where('min_amount','<=',$data['applied_amount'])
                                ->where('max_amount','>=',$data['applied_amount'])->first();

                LoanFee::create([
                    'loan_id' => $loan->id,
                    'insurance_monthly_fee' => $insurance->monthly_fee,
                    'insurance_annual_fee' => $insurance->annual_fee,
                    'monthly_amortization' => $data['monthly_amortization']
                ]);

            });

            Session::flash('Loan created successfully');
            return response()->json([
                'status' => 'success',
                'message' => 'Loan created successfully',
            ]);
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
        if($loan->status == LoanStatus::APPROVE->value)
            abort(404);

        $clients = Client::all();
        $loan_products = LoanProduct::active()->get();

        return view('loan.edit', compact('loan','clients','loan_products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {

        $request->validate([
            'loan_date' => 'required|date',
        ]);

        $request->merge([
            'applied_amount' => str_replace(',', '', $request->applied_amount),
            'principal' => str_replace(',', '', $request->applied_amount),
            'advance_interest' => str_replace(',', '', $request->advance_interest),
            'service_fee' => str_replace(',', '', $request->service_fee),
            'insurance_fee' => str_replace(',', '', $request->insurance_fee),
            'notary_fee' => str_replace(',', '', $request->notary_fee),
            'loan_no' => Loan::generateLoanNumber($request->loan_date)
        ]);

        $loan->loan_date = $request->loan_date;
        $loan->borrower_id = $request->borrower_id;
        $loan->loan_product_id = $request->loan_product_id;
        $loan->loan_duration = $request->loan_duration;
        $loan->interest_rate = $request->interest_rate;
        $loan->principal = $request->applied_amount;
        $loan->applied_amount = $request->applied_amount;

        $loan->loanCharges->advance_interest = $request->advance_interest;
        $loan->loanCharges->service_fee = $request->service_fee;
        $loan->loanCharges->insurance_fee = $request->insurance_fee;
        $loan->loanCharges->notary_fee = $request->notary_fee;

        $loan->loanCharges->save();
        $loan->save();

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

            $loans = Loan::pending()->with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)
                        ->orderByDesc('loan_date')
                        ->orderByDesc('loan_no')
                        ->get();
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

            $loans = Loan::approved()->with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)
                    ->whereNull('disbursed_date')
                    ->orderByDesc('loan_date')
                        ->orderByDesc('loan_no')
                        ->get();
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

            $loans = Loan::declined()->with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)
                        ->orderByDesc('loan_date')
                        ->orderByDesc('loan_no')
                        ->get();
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

            $loans = Loan::withdrawn()->with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)
                        ->orderByDesc('loan_date')
                        ->orderByDesc('loan_no')
                        ->get();
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

            $loans = Loan::closed()->with('loanProduct','loanProduct.loanType','borrower.details')->filter($search)->get();
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
            $loan->approved_date = date('Y-m-d H:i:s');
            $loan->approved_by = auth()->user()->id;
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
            $loan->declined_date = date('Y-m-d H:i:s');
            $loan->declined_by = auth()->user()->id;
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

    public function disburse(Loan $loan, Request $request)
    {
        if($loan) {
            $loan->disbursed_date = $request->disbursed_date;
            $loan->disbursed_notes = $request->notes;
            $loan->disbursed_by = auth()->user()->id;
            $loan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan successfully disbursed.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Loan not found'
        ], 404);
    }

    public function processReloan($data)
    {
        try {

            $loan_product = LoanProduct::with('prerequisite')->find($data['loan_product_id']);
            if ($loan_product->prerequisite->isNotEmpty()) {
                $principal_loan = Loan::where('borrower_id', $data['borrower_id'])
                    ->where('status', LoanStatus::APPROVE)
                    ->where('is_special', 0)
                    ->first();

                if (!in_array($principal_loan->loan_product_id,$loan_product->prerequisite->pluck('id')->toArray())) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Required prerequisite loan is missing.',
                    ], 400);
                }
            }

            DB::transaction(function () use ($data) {

                $previous_loan = Loan::with('payments','loanCharges')->where('loan_no', $data['loan_no_reference'])->first();
                $previous_loan->closed_date = $data['loan_date'];
                $previous_loan->status = LoanStatus::CLOSED;
                $previous_loan->save();

                $previous_loan_principal = $previous_loan->principal;
                $total_loan_payment = $previous_loan->payments->where('transaction_type', 'withdraw')->sum('amount');
                $previous_loan_balance = $previous_loan_principal - $total_loan_payment;

                // create saving for payment
                Savings::create([
                    'transaction_date' => $data['loan_date'],
                    'transaction_type' => 'deposit',
                    'client_id' => $data['borrower_id'],
                    'amount' => $previous_loan_balance,
                ]);

                Savings::create([
                    'transaction_date' => $data['loan_date'],
                    'transaction_type' => 'withdraw',
                    'client_id' => $data['borrower_id'],
                    'loan_id' => $previous_loan->id,
                    'amount' => $previous_loan_balance,
                    'principal' => $previous_loan_balance,
                ]);

                $loan = Loan::create($data);

                LoanCharge::create([
                    'loan_id' => $loan->id,
                    'advance_interest' => $data['advance_interest'],
                    'service_fee' => $data['service_fee'],
                    'insurance_fee' => $data['insurance_fee'],
                    'notary_fee' => $data['notary_fee'],
                    'reloan_deduction' => $previous_loan_balance,
                ]);

                $insurance = InsuranceFeeSchedule::where('min_amount','<=',$data['applied_amount'])
                                ->where('max_amount','>=',$data['applied_amount'])->first();

                LoanFee::create([
                    'loan_id' => $loan->id,
                    'insurance_monthly_fee' => $insurance->monthly_fee,
                    'insurance_annual_fee' => $insurance->annual_fee,
                    'monthly_amortization' => $data['monthly_amortization']
                ]);

            });

            Session::flash('Loan created successfully');
            return response()->json([
                'status' => 'success',
                'message' => 'Loan created successfully',
            ]);
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
    }

    public function getReloanDeduction(Request $request)
    {
        if($request->ajax()) {
            $loan = Loan::with('borrower.details','payments','loanCharges')->where('loan_no', $request->loan_no_reference)->first();
            $reloan_date = $request->reloan_date;
            $amount = $this->calculateReloanDeduction($loan, $reloan_date);

            return response()->json(['amount' => $amount]);
        }
    }

    function calculateReloanDeduction(Loan $previous_loan, $reloan_date)
    {
        $previous_loan_principal = $previous_loan?->principal;
        $payments = $previous_loan?->payments->where('transaction_type', 'withdraw');
        $last_payment_date = $payments?->sortByDesc('transaction_date')->value('transaction_date');
        $total_loan_payment = $payments->sum('principal');
        $principal_balance = $previous_loan_principal - $total_loan_payment;
        $advance_interest = $previous_loan->loanCharges->advance_interest;
        $reloan_deduction = 0;


        if($reloan_date == $last_payment_date) { # no interest due
            $reloan_deduction = $principal_balance - $advance_interest;
        } else { # calculate interest
            $unpaid_interest = 0;

            $start = Carbon::parse($last_payment_date)->addDay();
            $end = Carbon::parse($reloan_date);
            $number_of_days = (int)$start->diffInDays($end);
            Log::info($start);
            Log::info($number_of_days);

            $interest_rate = $previous_loan->interest_rate / 100;
            $interest = ($interest_rate/30) * $number_of_days * $principal_balance;
            $unpaid_interest = round($interest);

            $reloan_deduction = $principal_balance - ($advance_interest - $unpaid_interest);

            Log::info('principal_balance: '.$principal_balance);
            Log::info('advance_interest: '.$advance_interest);
            Log::info('unpaid_interest: '.$unpaid_interest);
            Log::info('reloan_deduction: '.$reloan_deduction);
        }

        return number_format($reloan_deduction,2);
    }
}
