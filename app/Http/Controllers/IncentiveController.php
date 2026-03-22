<?php

namespace App\Http\Controllers;

use App\Models\Incentives;
use Illuminate\Http\Request;

class IncentiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Incentives $incentives)
    {
        return response()->json([
            'status' => 'success',
            'data' => $incentives
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incentives $incentives)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incentives $incentives)
    {
        $request->validate([
            'transaction_date' => 'required',
            'description' => 'required|string',
            'amount' => 'required',
        ]);

        $incentives->transaction_date = $request->transaction_date;
        $incentives->description = $request->description;
        $incentives->amount = $request->amount;
        $incentives->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Incentives updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incentives $incentives)
    {
        $incentives->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Incentives deleted successfully',
        ]);
    }

    public function printReceipt($id)
    {
        $incentives = Incentives::with('borrower.details')->findOrFail($id);

        // Get branch name
        $branchName = $incentives->borrower->branch ? $incentives->borrower->branch->name : 'Unknown';

        // Custom title rules
        if (strcasecmp($branchName, 'Guiuan') === 0) {
            $branchTitle = 'Head Office';
        } else {
            $branchTitle = $branchName . ' Branch';
        }

        return view('client.incentives-receipt', compact('incentives', 'branchTitle'));
    }
}
