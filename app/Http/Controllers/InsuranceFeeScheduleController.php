<?php

namespace App\Http\Controllers;

use App\Models\InsuranceFeeSchedule;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class InsuranceFeeScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $insurance_fee_schedules = InsuranceFeeSchedule::filter($search)->get();
            $totalRecords = $insurance_fee_schedules->count();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $insurance_fee_schedules  // The paginated and formatted data
            ]);
        }


        return view('insurance-fee-schedule.index');
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
            InsuranceFeeSchedule::create($data);
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
            'message' => 'Insurance Fee Schedule created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(InsuranceFeeSchedule $insurance_fee_schedule)
    {
        if($insurance_fee_schedule) {
            return response()->json($insurance_fee_schedule);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Insurance Fee Schedule not found'
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InsuranceFeeSchedule $insurance_fee_schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InsuranceFeeSchedule $insurance_fee_schedule)
    {
        $request->validate([
            'min_amount' => 'required',
            'max_amount' => 'required',
            'type' => 'required',
            'fee' => 'required',
            'status' => 'required',
        ]);

        $insurance_fee_schedule->min_amount = $request->min_amount;
        $insurance_fee_schedule->max_amount = $request->max_amount;
        $insurance_fee_schedule->type = $request->type;
        $insurance_fee_schedule->fee = $request->fee;
        $insurance_fee_schedule->status = $request->status;
        $insurance_fee_schedule->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Insurance Fee Schedule updated successfully',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InsuranceFeeSchedule $insurance_fee_schedule)
    {
        $insurance_fee_schedule->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Insurance Fee Schedule deleted successfully',
        ]);
    }

    public function getInsuranceFee(Request $request)
    {
        $insurance_fee =  InsuranceFeeSchedule::where('min_amount','<=',$request->amount)
                ->where('max_amount','>=',$request->amount)->first();

        return response()->json([
            'monthly' => $insurance_fee->monthly_fee ?? 0,
            'annual' => $insurance_fee->annual_fee ?? 0,
        ]);
    }
}
