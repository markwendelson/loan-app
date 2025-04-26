<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientDetails;
use App\Services\ClientRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $clients = Client::with('details')->get();

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
        $client = ClientRegistrationService::register($request->all());

        if($client) {
            return redirect()->route('client.index')->with('message', 'Client created successfully');
        } else {
            return redirect()->route('client.index')->with('message', 'Something went wrong.');
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
    public function edit(Client $client)
    {
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
        //
    }
}
