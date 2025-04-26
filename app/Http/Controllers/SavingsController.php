<?php

namespace App\Http\Controllers;

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

            $savings = Savings::filter($search)->get();
            $totalRecords = $savings->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $savings  // The paginated and formatted data
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Savings $savings)
    {
        //
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
    public function update(Request $request, Savings $savings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Savings $savings)
    {
        //
    }
}
