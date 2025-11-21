<?php

namespace App\Http\Controllers;

use App\Models\CheckEncashment;
use Illuminate\Http\Request;

class CheckEncashmentController extends Controller
{
  
    public function index()
    {
        $checks = CheckEncashment::all();
        return response()->json($checks);
    }


     // Store a newly created resource in storage.
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transDate'       => 'required|date',
            'checkNo'         => 'required|string|max:50',
            'bankName'        => 'required|string|max:100',
            'checkAmount'     => 'required|numeric',
            'charge'          => 'nullable|numeric',
            'net_proceeds'    => 'required|numeric',
            'id_type'         => 'nullable|string|max:50',
            'id_number'       => 'nullable|string|max:50',
            'id_picture_path' => 'nullable|string|max:255',
        ]);

        $check = CheckEncashment::create($validated);

        return response()->json($check, 201);
    }

    public function show($id)
    {
        $check = CheckEncashment::find($id);

        if (!$check) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($check);
    }

  
   
    public function update(Request $request, $id)
    {
        $check = CheckEncashment::find($id);

        if (!$check) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $validated = $request->validate([
            'transDate'       => 'sometimes|date',
            'checkNo'         => 'sometimes|string|max:50',
            'bankName'        => 'sometimes|string|max:100',
            'checkAmount'     => 'sometimes|numeric',
            'charge'          => 'nullable|numeric',
            'net_proceeds'    => 'sometimes|numeric',
            'id_type'         => 'nullable|string|max:50',
            'id_number'       => 'nullable|string|max:50',
            'id_picture_path' => 'nullable|string|max:255',
        ]);

        $check->update($validated);

        return response()->json($check);
    }

    
    public function destroy($id)
    {
        $check = CheckEncashment::find($id);

        if (!$check) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $check->delete();

        return response()->json(['message' => 'Record deleted successfully']);
    }
}
