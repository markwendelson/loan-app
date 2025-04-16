<?php

namespace App\Http\Controllers;

use App\Models\NotaryFeeSchedule;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class NotaryFeeScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $notaryFees = NotaryFeeSchedule::filter($search)->get();
            $totalRecords = $notaryFees->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $notaryFees  // The paginated and formatted data
            ]);

            return response()->json($notaryFees);
        }


        return view('notary-fee-schedule.index');
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
        $data = $request->all();

        try {
            NotaryFeeSchedule::create($data);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Duplicate entry'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Notary Fee Schedule created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(NotaryFeeSchedule $notaryFee)
    {
        if($notaryFee) {
            return response()->json($notaryFee);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Notary Fee Schedule not found'
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NotaryFeeSchedule $notaryFee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NotaryFeeSchedule $notaryFee)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'status' => 'required',
        ]);

        $notaryFee->name = $request->name;
        $notaryFee->description = $request->description;
        $notaryFee->loan_type_id = $request->loan_type_id;
        $notaryFee->status = $request->status;
        $notaryFee->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Notary Fee Schedule updated successfully',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NotaryFeeSchedule $notaryFee)
    {
        $notaryFee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notary Fee Schedule deleted successfully',
        ]);
    }
}
