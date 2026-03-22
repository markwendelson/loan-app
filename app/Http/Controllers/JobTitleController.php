<?php

namespace App\Http\Controllers;

use App\Models\JobTitle;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class JobTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $jobs = JobTitle::filter($search);

            $start = $request->input('start', 0);
            $length = $request->input('length', 5);
            $totalRecords = $jobs->count();
            $jobs = $jobs->skip($start)
                            ->take($length)
                            ->get();

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $jobs  // The paginated and formatted data
            ]);
        }

        return view('job.index');
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
            JobTitle::create($data);
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
            'message' => 'Job Title created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $jobTitle = JobTitle::find($id);

        if($jobTitle) {
            return response()->json($jobTitle);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Job Title not found'
        ], 404);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobTitle $jobTitle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobTitle $jobTitle)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'status' => 'required',
        ]);

        $jobTitle->name = $request->name;
        $jobTitle->description = $request->description;
        $jobTitle->status = $request->status;
        $jobTitle->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Job Title updated successfully',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $jobTitle = JobTitle::find($id);
        $jobTitle->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Job Title deleted successfully',
        ]);
    }
}
