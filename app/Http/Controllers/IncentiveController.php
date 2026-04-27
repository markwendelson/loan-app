<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
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
    public function show(int $id)
    {
        $incentives = Incentives::find($id);

        if(!$incentives) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incentives not found',
            ], 404);
        }

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
    public function update(Request $request, int $id)
    {
        $request->validate([
            'transaction_date' => 'required',
            'description' => 'required|string',
            'amount' => 'required',
        ]);

        $incentives = Incentives::find($id);

        if(!$incentives) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incentives not found',
            ], 404);
        }

        $incentives->transaction_date = $request->transaction_date;
        $incentives->description = $request->description;
        $incentives->transaction_code = $request->transaction_code;
        $incentives->mode_of_payment = $request->mode_of_payment;
        $incentives->amount = str_replace(",", "", $request->amount);
        $incentives->save();

        if ($request->hasFile('attachment')) {

            $file = $request->file('attachment');
            $filename = $file->getClientOriginalName();
            $folder = 'attachments/' . $incentives->incentive_no;
            $path = $file->storeAs($folder, $filename, 'public');

            Attachment::create([
                'file_reference' => 'incentives',
                'transaction_no' => $incentives->incentive_no,
                'file_name' => $filename,
                'file_path' => $path,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Incentives updated successfully',
            'id' => $incentives->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $incentives = Incentives::find($id);

        if(!$incentives) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incentives not found',
            ], 404);
        }

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
