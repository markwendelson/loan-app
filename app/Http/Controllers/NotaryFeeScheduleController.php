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

            $notary_fee_schedules = NotaryFeeSchedule::filter($search)->get();
            $totalRecords = $notary_fee_schedules->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $notary_fee_schedules  // The paginated and formatted data
            ]);

            return response()->json($notary_fee_schedules);
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
    public function show(NotaryFeeSchedule $notary_fee_schedule)
    {
        if($notary_fee_schedule) {
            return response()->json($notary_fee_schedule);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Notary Fee Schedule not found'
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NotaryFeeSchedule $notary_fee_schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NotaryFeeSchedule $notary_fee_schedule)
    {
        $request->validate([
            'min_amount' => 'required',
            'max_amount' => 'required',
            'type' => 'required',
            'fee' => 'required',
            'status' => 'required',
        ]);

        $notary_fee_schedule->min_amount = $request->min_amount;
        $notary_fee_schedule->max_amount = $request->max_amount;
        $notary_fee_schedule->type = $request->type;
        $notary_fee_schedule->fee = $request->fee;
        $notary_fee_schedule->status = $request->status;
        $notary_fee_schedule->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Notary Fee Schedule updated successfully',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NotaryFeeSchedule $notary_fee_schedule)
    {
        $notary_fee_schedule->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notary Fee Schedule deleted successfully',
        ]);
    }
}
