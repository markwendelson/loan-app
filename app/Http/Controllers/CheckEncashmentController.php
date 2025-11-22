<?php

namespace App\Http\Controllers;

use App\Models\CheckEncashment;
use Illuminate\Http\Request;

class CheckEncashmentController extends Controller
{
  
    public function index()
    {
         $checks = CheckEncashment::all();
         return view('others.check-encashments.index', compact('checks'));
    }


   public function store(Request $request)
    {
          // Log the entire request
    logger('Incoming request data:', $request->all());

        $validated = $request->validate([
            'transDate'       => 'required|date',
            'checkNo'         => 'required|string|max:50',
            'bankName'        => 'required|string|max:100',
            'checkAmount'     => 'required|numeric',
            'charge'          => 'nullable|numeric',
            'net_proceeds'    => 'required|numeric',
            'id_type'         => 'nullable|string|max:50',
            'id_number'       => 'nullable|string|max:50',
            'id_picture'      => 'nullable|image|max:2048', // file upload
        ]);

  // Log the validated data
    logger('Validated data:', $validated);

        // Handle file upload
        if ($request->hasFile('id_picture')) {
            $validated['id_picture_path'] = $request->file('id_picture')
                ->store('id_pictures', 'public'); 
        }

        $check = CheckEncashment::create($validated);

        return response()->json($check, 201);
    }

  

public function show($id)
{
  
    $check = CheckEncashment::find($id);
    if (!$check) {
        return response()->json(['message' => 'Record not found'], 404);
    }

    // format transDate
    $check->transDate = $check->transDate->format('Y-m-d');

      
   
    // Log the formatted check data
    logger('Formatted Check Data:', $check->toArray());


   return response()->json([
    'id' => $check->id,
    'transDate' => $check->transDate->format('Y-m-d'),
    'checkNo' => $check->checkNo,
    'bankName' => $check->bankName,
    'checkAmount' => $check->checkAmount,
    'charge' => $check->charge,
    'net_proceeds' => $check->net_proceeds,
    'id_type' => $check->id_type,
    'id_number' => $check->id_number,
    'id_picture_path' => $check->id_picture_path,
]);
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
        'id_picture'      => 'nullable|image|max:2048', // <-- file input
    ]);

    // Handle file upload
    if ($request->hasFile('id_picture')) {
        // Optionally delete old file
        if ($check->id_picture_path && \Storage::disk('public')->exists($check->id_picture_path)) {
            \Storage::disk('public')->delete($check->id_picture_path);
        }

        $validated['id_picture_path'] = $request->file('id_picture')
            ->store('id_pictures', 'public');
    }

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
