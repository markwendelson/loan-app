<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Client;
use App\Models\ClientDetails;
use App\Models\Loan;
use App\Services\ClientRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        if($request->ajax()) {
            $search = $request->search['value'];

            // $clients = Client::whereHas('coMakers')->with('details')->get();
            $clients = Client::filter($search)->with('details')->get();

            // $mappedClients = $clients->map(function($client) {
            //     $client->fullname = trim(collect([$client->firstname, $client->middlename, $client->lastname, ])->filter()->implode(' '));
            //     $client->present_full_address = trim(collect([$client->present_address,$client->presentCity->name ?? '',$client->presentProvince->name ?? '',])->filter()->implode(' '));
            //     return $client;
            // });

            $totalRecords = $clients->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $clients  // The paginated and formatted data
            ]);

        }

        return view('client.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = $this->branches;
        $provinces = $this->provinces;
        $job_titles = $this->job_titles;
        $nationalities = $this->nationalities;
        $banks = $this->banks;

        return view('client.create', compact('branches','provinces', 'job_titles','nationalities','banks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $client = ClientRegistrationService::register($request->all());

        if($client) {
            return redirect()->route('client.index')->with('message', 'Client created successfully');
        } else {
            return redirect()->back()->withInput()->with('message', 'Something went wrong.');
        }

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Client created successfully',
        // ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Client $client)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            // $clients = Client::whereHas('coMakers')->with('details')->get();
            $clients = Client::filter($search)->with('details')->where('id','!=',$client->id)->get();

            // $mappedClients = $clients->map(function($client) {
            //     $client->fullname = trim(collect([$client->firstname, $client->middlename, $client->lastname, ])->filter()->implode(' '));
            //     $client->present_full_address = trim(collect([$client->present_address,$client->presentCity->name ?? '',$client->presentProvince->name ?? '',])->filter()->implode(' '));
            //     return $client;
            // });

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

        return view('client.edit', compact('client','branches','provinces', 'job_titles','nationalities','banks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Client deleted successfully',
        ]);
    }

    public function ledgerPrincipal(Request $request, Client $client)
    {
        $loans = Loan::with('payments','loanCharges')->where('borrower_id', $client->id)->where('status',LoanStatus::APPROVE)->get();
        $filter = '';
        foreach($loans as $loan) {
            $loan_prerequisites = DB::table('loan_product_prerequisites')->where('loan_product_id', $loan->loan_product_id)->get();
            if(count($loan_prerequisites) == 0) {
                $filter = $loan->loan_product_id;
                break;
            }
        }

        $mappedData = $loans->filter(fn($loan) => $loan->loan_product_id == $filter)->flatMap(function ($loan) {
            $interest = $loan->loanCharges->advance_interest;
            $service_fee = $loan->loanCharges->service_fee;
            $insurance_fee = $loan->loanCharges->insurance_fee;
            $notary_fee = $loan->loanCharges->notary_fee;

            $loan_charges = $interest + $service_fee + $insurance_fee + $notary_fee;
            $net_proceed = $loan->principal - $loan_charges;

            $balance = $loan->principal;

            // Initial loan row
            $rows = [[
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
                'payment_interest' => null,
                'payment_insurance' => null,
                'payment_principal' => null,
                'change' => null,
                'balance' => number_format($balance, 2),
                'remarks' => null,
            ]];

            // Running total
            $total_payment_principal = 0;

            foreach ($loan->payments->where('transaction_type', 'withdraw') as $payment) {
                $total_payment_principal += $payment->principal;
                $balance = $loan->principal - $total_payment_principal;

                $rows[] = [
                    'loan_id' => $loan->id,
                    'payment_id' => $payment->id,
                    'transaction_date' => null,
                    'withdraw_date' => $payment->transaction_date,
                    'salary_month' => null,
                    'loan_principal' => null,
                    'loan_interest' => null,
                    'loan_service_fee' => null,
                    'loan_insurance_fee' => null,
                    'others' => null,
                    'net_proceed' => null,
                    'signature' => null,
                    'wdl' => number_format($payment->amount, 2),
                    'payment_interest' => number_format($payment->interest, 2),
                    'payment_insurance' => number_format($payment->insurance, 2),
                    'payment_principal' => number_format($payment->principal, 2),
                    'change' => null,
                    'balance' => number_format($balance, 2),
                    'remarks' => null,
                ];
            }

            return collect($rows);
        });

        if($request->ajax()) {
            $totalRecords = 0;
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $mappedData
            ]);
        }

        $loan = [
            'principal_loan_no' => $loans[0]['loan_no'] ?? '',
            'principal_date_granted' => $loans[0]['approved_date'] ?? '',
            'special_loan_no' => $loans[1]['loan_no'] ?? '',
            'special_date_granted' => $loans[1]['approved_date'] ?? ''
        ];

        return view('client.ledger', compact('client', 'loan'));
    }

    public function ledgerSpecial(Request $request, Client $client)
    {
        $loans = Loan::with('payments','loanCharges')->where('borrower_id', $client->id)->where('status',LoanStatus::APPROVE)->get();

        $filter = '';
        foreach($loans as $loan) {
            $loan_prerequisites = DB::table('loan_product_prerequisites')->where('loan_product_id', $loan->loan_product_id)->get();
            if(count($loan_prerequisites) == 0) {
                $filter = $loan->loan_product_id;
                break;
            }
        }

        $mappedData = $loans->filter(fn($loan) => $loan->loan_product_id !== $filter)->flatMap(function ($loan) {
            $interest = $loan->loanCharges->advance_interest;
            $service_fee = $loan->loanCharges->service_fee;
            $insurance_fee = $loan->loanCharges->insurance_fee;
            $notary_fee = $loan->loanCharges->notary_fee;

            $loan_charges = $interest + $service_fee + $insurance_fee + $notary_fee;
            $net_proceed = $loan->principal - $loan_charges;

            $balance = $loan->principal;

            // Initial loan row
            $rows = [[
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
                'payment_interest' => null,
                'payment_insurance' => null,
                'payment_principal' => null,
                'change' => null,
                'balance' => number_format($balance, 2),
                'remarks' => null,
            ]];

            // Running total
            $total_payment_principal = 0;

            foreach ($loan->payments->where('transaction_type', 'withdraw') as $payment) {
                $total_payment_principal += $payment->principal;
                $balance = $loan->principal - $total_payment_principal;

                $rows[] = [
                    'loan_id' => $loan->id,
                    'payment_id' => $payment->id,
                    'transaction_date' => null,
                    'withdraw_date' => $payment->transaction_date,
                    'salary_month' => null,
                    'loan_principal' => null,
                    'loan_interest' => null,
                    'loan_service_fee' => null,
                    'loan_insurance_fee' => null,
                    'others' => null,
                    'net_proceed' => null,
                    'signature' => null,
                    'wdl' => number_format($payment->amount, 2),
                    'payment_interest' => number_format($payment->interest, 2),
                    'payment_insurance' => number_format($payment->insurance, 2),
                    'payment_principal' => number_format($payment->principal, 2),
                    'change' => null,
                    'balance' => number_format($balance, 2),
                    'remarks' => null,
                ];
            }

            return collect($rows);
        });

        if($request->ajax()) {
            $totalRecords = 0;
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $mappedData
            ]);
        }
    }
}
