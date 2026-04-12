<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Client;
use App\Models\ClientDetails;
use App\Models\ClientDocument;
use App\Models\Loan;
use App\Services\ClientRegistrationService;
use App\Services\ClientUpdateService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public $branches;
    public $provinces;
    public $job_titles;
    public $nationalities;
    public $banks;

    public function __construct()
    {
        $this->branches = getBranches();
        $this->provinces = getProvinces();
        $this->job_titles = getJobTitles();
        $this->nationalities = getNationalities();
        $this->banks = getBanks();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->search['value'];

            // $clients = Client::whereHas('coMakers')->with('details')->get();
            if (!auth()->user()->hasRole('admin|superadmin')) {
                $clients = Client::where('branch_id', auth()->user()->branch_id)
                    ->filter($search)
                    ->with('details');
            } else {
                $clients = Client::filter($search)->with('details');
            }

            // $mappedClients = $clients->map(function($client) {
            //     $client->fullname = trim(collect([$client->firstname, $client->middlename, $client->lastname, ])->filter()->implode(' '));
            //     $client->present_full_address = trim(collect([$client->present_address,$client->presentCity->name ?? '',$client->presentProvince->name ?? '',])->filter()->implode(' '));
            //     return $client;
            // });

            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $clients->count();
            $clients = $clients->skip($start)
            ->take($length)
            ->get();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $clients  // The paginated and formatted data
            ]);

        }

        return view('client.index');
    }


    public function create()
    {
        $branches = $this->branches;
        $provinces = $this->provinces;
        $job_titles = $this->job_titles;
        $nationalities = $this->nationalities;
        $banks = $this->banks;

        return view('client.create', compact('branches', 'provinces', 'job_titles', 'nationalities', 'banks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $client = ClientRegistrationService::register($request->all());

        if ($client['status'] == 'success') {
            return redirect()->route('client.index')->with('message', $client['message']);
        } else {
            return redirect()->back()->withInput()->with('message', $client['message']);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $branches = $this->branches;
        $provinces = $this->provinces;
        $job_titles = $this->job_titles;
        $nationalities = $this->nationalities;
        $banks = $this->banks;

        return view('client.show', compact('client', 'branches', 'provinces', 'job_titles', 'nationalities', 'banks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Client $client)
    {
        if ($request->ajax()) {
            $search = $request->search['value'];

            $clients = Client::filter($search)->with('details')->where('id', '!=', $client->id)->get();

            $totalRecords = $clients->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $clients  // The paginated and formatted data
            ]);

        }

        $branches = $this->branches;
        $provinces = $this->provinces;
        $job_titles = $this->job_titles;
        $nationalities = $this->nationalities;
        $banks = $this->banks;

        return view('client.edit', compact('client', 'branches', 'provinces', 'job_titles', 'nationalities', 'banks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $updatedClient = ClientUpdateService::update($client, $request->all());

        return redirect()->route('client.show', $client->id)->with('message', 'Client updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $exists = Loan::where('borrower_id', $client->id)->first();

        if($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client have existing transactions',
            ]);
        }

        $client->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Client deleted successfully',
        ]);
    }

    public function ledgerPrincipal(Request $request, Client $client)
    {
        $loans = Loan::with('payments', 'loanCharges')->where('borrower_id', $client->id)->where('status', LoanStatus::APPROVE)->orderBy('is_special')->get();
        $filter = '';
        foreach ($loans as $loan) {
            $loan_prerequisites = DB::table('loan_product_prerequisites')->where('loan_product_id', $loan->loan_product_id)->get();
            if (count($loan_prerequisites) == 0) {
                $filter = $loan->loan_product_id;
                break;
            }
        }

        // Running total
        $total_payment_principal = 0;
        $balance = 0;
        $i = 0;
        $rawRows = $loans->filter(fn($loan) => $loan->loan_product_id == $filter)->flatMap(function ($loan) use (&$balance, &$total_payment_principal, &$i) {
            $interest = $loan->loanCharges->advance_interest;
            $service_fee = $loan->loanCharges->service_fee;
            $insurance_fee = $loan->loanCharges->insurance_fee;
            $notary_fee = $loan->loanCharges->notary_fee;
            $reloan_deduction = $loan->loanCharges->reloan_deduction;

            $loan_charges = $interest + $service_fee + $insurance_fee + $notary_fee + $reloan_deduction;
            $net_proceed = $loan->principal - $loan_charges;
            $i++;
            // Initial loan row
            $rows = [
                [
                    'index' => $i,
                    'loan_id' => $loan->id,
                    'payment_id' => null,
                    'transaction_date' => $loan->loan_date,
                    'withdraw_date' => null,
                    'salary_month' => null,
                    'loan_principal' => number_format($loan->principal, 2),
                    'loan_interest' => number_format($interest, 2),
                    'loan_service_fee' => number_format($service_fee, 2),
                    'loan_insurance_fee' => number_format($insurance_fee, 2),
                    'others' => number_format($notary_fee, 2),
                    'net_proceed' => number_format($net_proceed, 2),
                    'signature' => null,
                    'wdl' => null,
                    'payment_date' => null,
                    'payment_interest' => null,
                    'payment_interest_month' => null,
                    'payment_insurance' => null,
                    'payment_insurance_month' => null,
                    'payment_principal' => number_format($reloan_deduction, 2),
                    'change' => null,
                    'transaction_code' => '',
                    'change_mop' => '',
                    'balance' => null, // to be filled after sorting
                    'remarks' => null,
                    'attachment' => null,
                    'interest_deficit' => null,
                    'order_date' => Carbon::parse($loan->loan_date),
                    'raw_principal' => $loan->principal, // used for recomputing
                ]
            ];

            foreach ($loan->payments->where('transaction_type', 'withdraw') as $payment) {
                $i++;
                // $total_payment_principal += $payment->principal;
                // $balance = $loan->principal - $total_payment_principal;
                $change = $payment->amount - ($payment->interest + $payment->insurance + $payment->principal);

                $rows[] = [
                    'index' => $i,
                    'loan_id' => $loan->id,
                    'payment_id' => $payment->id,
                    'transaction_date' => null,
                    'withdraw_date' => $payment->transaction_date,
                    'salary_month' => strtoupper(date('M y', strtotime($payment->transaction_date))),
                    'loan_principal' => null,
                    'loan_interest' => null,
                    'loan_service_fee' => null,
                    'loan_insurance_fee' => null,
                    'others' => null,
                    'net_proceed' => null,
                    'signature' => null,
                    'wdl' => number_format($payment->amount, 2),
                    'payment_date' => $payment->transaction_date,
                    'payment_interest' => number_format($payment->interest, 2),
                    'payment_interest_month' => (!empty($payment->interest_end_date))
                        ? date('d-M-y', strtotime($payment->interest_end_date))
                        : null,
                    'payment_insurance' => number_format($payment->insurance, 2),
                    'payment_insurance_month' => (!empty($payment->insurance_month) && !empty($payment->insurance_year))
                        ? (new DateTime("{$payment->insurance_year}-{$payment->insurance_month}-01"))->format('M y')
                        : null,
                    'payment_principal' => number_format($payment->principal, 2),
                    'change' => number_format($change, 2),
                    'transaction_code' => $payment->transaction_code ?? '',
                    'change_mop' => ucwords(str_replace("_"," ",$payment->change_mop)),
                    'balance' => null, // to be computed
                    'remarks' => "{$payment->mode_of_payment} - <a href='" . route('savings.print', $payment->id) . "' target='_blank'>{$payment->transaction_no}</a>",
                    'attachment' => isset($payment->attachments->file_path) ? asset('storage/' . $payment->attachments->file_path) : '',
                    'order_date' => Carbon::parse($payment->transaction_date),
                    'raw_principal' => -1 * $payment->principal ?? 0, // subtract from balance
                    'interest_deficit' => number_format($payment->interest_deficit, 2),
                ];
            }
            return collect($rows);
        });

        $sortedRows = $rawRows->sortBy([
            ['order_date', 'asc'],
            ['index', 'asc'],
        ])->values();

        $balance = 0;
        $balancedRows = $sortedRows->map(function ($row) use (&$balance) {
            $delta = $row['raw_principal'] ?? 0;
            $balance += $delta;

            $row['balance'] = number_format($balance, 2);
            unset($row['raw_principal']); // cleanup
            return $row;
        });

        // Step 3: Sort again to DESCENDING if needed for display
        $mappedData = $balancedRows->sortByDesc([
            ['order_date', 'asc'],
            ['index', 'asc'],
        ])->values();

        if ($request->ajax()) {
            $totalRecords = 0;
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $mappedData
            ]);
        }


        $principal_loan = $loans[0] ?? null;
        $special_loan = $loans[1] ?? null;
        $principal_attachments = null;
        $special_attachments = null;

        if($principal_loan) {
            $principal_attachments = ClientDocument::where('loan_id', $principal_loan->id)->get();
        }

        if($special_attachments) {
            $special_attachments = ClientDocument::where('loan_id', $special_loan->id)->get();
        }

        $loan = [
            'principal_loan_no' => $principal_loan->loan_no ?? '',
            'principal_loan_product' => $principal_loan->loanProduct->name ?? '',
            'principal_loan_product_type' => $principal_loan->loanProduct->loanType->name ?? '',
            'principal_date_granted' => $principal_loan->approved_date ?? '',
            'special_loan_no' => $special_loan->loan_no ?? '',
            'special_loan_product' => $special_loan->loanProduct->name ?? '',
            'special_date_granted' => $special_loan->approved_date ?? '',
        ];

        $loan_is_co_maker = $client->isCoMakerFor()
            ->with('details')
            ->get()
            ->map(function ($comaker) {
                $client_loan = Loan::where('id', $comaker->pivot->loan_id)->first();
                $comaker->loan_balance = $client_loan == null ? 'Loan ID not specified' : getLoanBalance($client_loan);
                return $comaker;
            });

        return view('client.ledger', compact('client', 'loan', 'principal_loan', 'special_loan', 'loan_is_co_maker', 'principal_attachments', 'special_attachments'));
    }

    public function ledgerSpecial(Request $request, Client $client)
    {
        $loans = Loan::with('payments', 'loanCharges')->where('borrower_id', $client->id)->where('status', LoanStatus::APPROVE)->get();

        $filter = '';
        foreach ($loans as $loan) {
            $loan_prerequisites = DB::table('loan_product_prerequisites')->where('loan_product_id', $loan->loan_product_id)->get();
            if (count($loan_prerequisites) == 0) {
                $filter = $loan->loan_product_id;
                break;
            }
        }

        $i = 0;
        $rawRows = $loans->filter(fn($loan) => $loan->loan_product_id !== $filter)->flatMap(function ($loan) use (&$i) {
            $interest = $loan->loanCharges->advance_interest;
            $service_fee = $loan->loanCharges->service_fee;
            $insurance_fee = $loan->loanCharges->insurance_fee;
            $notary_fee = $loan->loanCharges->notary_fee;
            $reloan_deduction = $loan->loanCharges->reloan_deduction;

            $loan_charges = $interest + $service_fee + $insurance_fee + $notary_fee;
            $net_proceed = $loan->principal - $loan_charges;
            $i++;
            // Initial loan row
            $rows = [
                [
                    'index' => $i,
                    'loan_id' => $loan->id,
                    'payment_id' => null,
                    'transaction_date' => $loan->loan_date,
                    'description' => $loan->description,
                    'loan_principal' => number_format($loan->principal, 2),
                    'loan_interest' => number_format($interest, 2),
                    'loan_service_fee' => number_format($service_fee, 2),
                    'loan_insurance_fee' => number_format($insurance_fee, 2),
                    'others' => number_format($notary_fee, 2),
                    'net_proceed' => number_format($net_proceed, 2),
                    'signature' => null,
                    'wdl' => null,
                    'payment_date' => null,
                    'payment_interest' => null,
                    'payment_insurance' => null,
                    'payment_principal' => number_format($reloan_deduction, 2),
                    'change' => null,
                    'change_mop' => null,
                    'transaction_code' => '',
                    'change_mop' => '',
                    'balance' => null, // to be computed
                    'remarks' => null,
                    'attachment' => null,
                    'order_date' => Carbon::parse($loan->loan_date),
                    'raw_principal' => $loan->principal, // used for recomputing
                    'interest_deficit' => null,
                ]
            ];

            // Running total
            $total_payment_principal = 0;

            foreach ($loan->payments->where('transaction_type', 'withdraw') as $payment) {
                $total_payment_principal += $payment->principal;
                // $balance = $loan->principal - $total_payment_principal;
                $change = $payment->amount - ($payment->interest + $payment->insurance + $payment->principal);

                $rows[] = [
                    'index' => $i,
                    'loan_id' => $loan->id,
                    'payment_id' => $payment->id,
                    'transaction_date' => null,
                    'description' => null,
                    'loan_principal' => null,
                    'loan_interest' => null,
                    'loan_service_fee' => null,
                    'loan_insurance_fee' => null,
                    'others' => null,
                    'net_proceed' => null,
                    'signature' => null,
                    'wdl' => number_format($payment->amount, 2),
                    'payment_date' => $payment->transaction_date,
                    'payment_interest' => number_format($payment->interest, 2),
                    'payment_insurance' => number_format($payment->insurance, 2),
                    'payment_principal' => number_format($payment->principal, 2),
                    'change' => number_format($change, 2),
                    'change_mop' => $payment->change_mop,
                    'transaction_code' => $payment->transaction_code ?? '',
                    'change_mop' => ucwords(str_replace("_"," ",$payment->change_mop)),
                    'balance' => null, // to be computed
                    'remarks' => null,
                    'attachment' => isset($payment->attachments->file_path) ? asset('storage/' . $payment->attachments->file_path) : '',
                    'order_date' => Carbon::parse($payment->transaction_date),
                    'raw_principal' => -1 * $payment->principal ?? 0, // subtract from balance
                    'interest_deficit' => number_format($payment->interest_deficit, 2),
                ];
            }

            return collect($rows);
        });

        $sortedRows = $rawRows->sortBy([
            ['order_date', 'asc'],
            ['index', 'asc'],
        ])->values();

        $balance = 0;
        $balancedRows = $sortedRows->map(function ($row) use (&$balance) {
            $delta = $row['raw_principal'] ?? 0;
            $balance += $delta;

            $row['balance'] = number_format($balance, 2);
            unset($row['raw_principal']); // cleanup
            return $row;
        });

        // Step 3: Sort again to DESCENDING if needed for display
        $mappedData = $balancedRows->sortByDesc([
            ['order_date', 'asc'],
            ['index', 'asc'],
        ])->values();

        if ($request->ajax()) {
            $totalRecords = 0;
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $mappedData
            ]);
        }
    }

    public function ledgerLoan(Request $request, Client $client, string $loan_no)
    {
        $loans = Loan::with('payments', 'loanCharges')->where(function ($q) use ($loan_no) {
            $q->where('loan_no', $loan_no)
                ->orWhere('reference_loan_no', $loan_no)
                ->where('is_additional_loan', 1)
                ->orWhere('is_reloan', 0);
        })
            ->where('borrower_id', $client->id)->get();

        $i = 0;
        // Running total
        $rawRows = $loans->flatMap(function ($loan) use (&$i) {
            $i++;
            $interest = $loan->loanCharges->advance_interest;
            $service_fee = $loan->loanCharges->service_fee;
            $insurance_fee = $loan->loanCharges->insurance_fee;
            $notary_fee = $loan->loanCharges->notary_fee;
            $reloan_deduction = $loan->loanCharges->reloan_deduction;

            $loan_charges = $interest + $service_fee + $insurance_fee + $notary_fee + $reloan_deduction;
            $net_proceed = $loan->principal - $loan_charges;

            $rows = [
                [
                    'index' => $i,
                    'loan_id' => $loan->id,
                    'payment_id' => null,
                    'transaction_date' => $loan->loan_date,
                    'withdraw_date' => null,
                    'salary_month' => null,
                    'loan_principal' => number_format($loan->principal, 2),
                    'loan_interest' => number_format($interest, 2),
                    'loan_service_fee' => number_format($service_fee, 2),
                    'loan_insurance_fee' => number_format($insurance_fee, 2),
                    'others' => number_format($notary_fee, 2),
                    'net_proceed' => number_format($net_proceed, 2),
                    'signature' => null,
                    'wdl' => null,
                    'payment_date' => null,
                    'payment_interest' => null,
                    'payment_interest_month' => null,
                    'payment_insurance' => null,
                    'payment_insurance_month' => null,
                    'payment_principal' => number_format($reloan_deduction, 2),
                    'change' => null,
                    'balance' => null, // to be filled after sorting
                    'remarks' => null,
                    'order_date' => Carbon::parse($loan->loan_date),
                    'raw_principal' => $loan->principal, // used for recomputing
                    'interest_deficit' => null,
                ]
            ];

            foreach ($loan->payments->where('transaction_type', 'withdraw') as $payment) {
                $change = $payment->amount - ($payment->interest + $payment->insurance + $payment->principal);

                $rows[] = [
                    'index' => $i,
                    'loan_id' => $loan->id,
                    'payment_id' => $payment->id,
                    'transaction_date' => null,
                    'withdraw_date' => $payment->transaction_date,
                    'salary_month' => strtoupper(date('M y', strtotime($payment->transaction_date))),
                    'loan_principal' => null,
                    'loan_interest' => null,
                    'loan_service_fee' => null,
                    'loan_insurance_fee' => null,
                    'others' => null,
                    'net_proceed' => null,
                    'signature' => null,
                    'wdl' => number_format($payment->amount, 2),
                    'payment_date' => $payment->transaction_date,
                    'payment_interest' => number_format($payment->interest, 2),
                    'payment_interest_month' => (!empty($payment->interest_end_date))
                        ? date('d-M-y', strtotime($payment->interest_end_date))
                        : null,
                    'payment_insurance' => number_format($payment->insurance, 2),
                    'payment_insurance_month' => (!empty($payment->insurance_month) && !empty($payment->insurance_year))
                        ? (new DateTime("{$payment->insurance_year}-{$payment->insurance_month}-01"))->format('M y')
                        : null,
                    'payment_principal' => number_format($payment->principal, 2),
                    'change' => number_format($change, 2),
                    'balance' => null, // to be computed
                    'remarks' => null,
                    'order_date' => Carbon::parse($payment->transaction_date),
                    'raw_principal' => -1 * $payment->principal, // subtract from balance
                    'interest_deficit' => number_format($payment->interest_deficit),
                ];
            }

            return $rows;
        });

        $sortedRows = $rawRows->sortBy([
            ['order_date', 'asc'],
            ['index', 'asc'],
        ])->values();

        $balance = 0;
        $balancedRows = $sortedRows->map(function ($row) use (&$balance) {
            $delta = ($row['raw_principal'] ?? 0);
            $balance += $delta;

            $row['balance'] = number_format($balance, 2);
            unset($row['raw_principal']); // cleanup
            return $row;
        });

        // Step 3: Sort again to DESCENDING if needed for display
        $mappedData = $balancedRows->sortByDesc([
            ['order_date', 'asc'],
            ['index', 'asc'],
        ])->values();

        $principal_loan = $loans[0] ?? null;

        $loan = [
            'principal_loan_no' => $principal_loan->loan_no ?? '',
            'principal_loan_product' => $principal_loan->loanProduct->name ?? '',
            'principal_loan_product_type' => $principal_loan->loanProduct->loanType->name ?? '',
            'principal_date_granted' => $principal_loan->approved_date ?? '',
        ];

        if ($request->ajax()) {
            $totalRecords = 0;
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $mappedData
            ]);
        }

        return view('client.ledger-loan', compact('client', 'loan'));
    }

    public function loanHistory(Client $client)
    {
        $loans = Loan::where('borrower_id', $client->id)->orderBy('status')->orderByDesc('loan_date')->where('is_additional_loan', 0)->get();
        return view('client.loan-history', compact('client', 'loans'));
    }


    public function loanHistoryDetails(Request $request, Client $client)
    {
        $loan = Loan::with('loanCharges')->where([
            'id' => $request->loan_id,
            'borrower_id' => $client->id
        ])->first();

        return view('client.loan-history-details', compact('client', 'loan'));
    }

    public function partialLedger(Request $request, Client $client)
    {

        $loan = Loan::with('payments', 'loanCharges')->where([
            'id' => $request->loan_id,
            'borrower_id' => $client->id
        ])->first();

        return view('client.partial-ledger', compact('client', 'loan'));
    }

    public function getBalance(Client $client)
    {
        $balance = getClientBalance($client);
        return response()->json(['balance' => $balance]);
    }
}
