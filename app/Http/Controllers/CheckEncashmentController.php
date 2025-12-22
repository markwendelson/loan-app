<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\CheckEncashment;
use Illuminate\Http\Request;

class CheckEncashmentController extends Controller
{
  
    public function index()
    {
         $checks = CheckEncashment::all();

        // Get all active percentages for 'check'
         $serviceCharges = DB::table('service_charges')
             ->where('is_active', 1)
             ->where('fee_type', 'check')
             ->get(); 

        // dd($serviceCharges);
         return view('others.check-encashments.index', compact('checks', 'serviceCharges'));
        //  return view('others.check-encashments.index', compact('checks'));
    }


   public function store(Request $request)
    {
          // Log the entire request
    logger('Incoming request data:', $request->all());

        $validated = $request->validate([
            'transDate'       => 'required|date',
            'checkNo'         => 'required|string|max:50',
            'last_name'       => 'required|string|max:100',
            'first_name'      => 'required|string|max:100',
            'middle_name'     => 'required|string|max:100',
            'bankName'        => 'required|string|max:100',
            'checkAmount'     => 'required|numeric',
            'charge'          => 'nullable|numeric',
            'no_service_charge' => 'nullable|boolean',
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

   if ($request->boolean('no_service_charge')) {
        $validated['charge'] = 0;
        $validated['net_proceeds'] = $request->input('checkAmount');
    }


        $check = CheckEncashment::create($validated);

      return response()->json([
            'data' => $check,
            'print_url' => route('check-encashments.receipt', $check->id),
        ], 201);
        // return response()->json($check, 201);
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
     'last_name' => $check->last_name,
    'first_name' => $check->first_name,
    'middle_name' => $check->middle_name,
    'customer_name' => $check->customer_name,
    'bankName' => $check->bankName,
    'checkAmount' => $check->checkAmount,
    'charge' => $check->charge,
    'net_proceeds' => $check->net_proceeds,
    'id_type' => $check->id_type,
    'id_number' => $check->id_number,
    'id_picture_path' => $check->id_picture_path,
    'no_service_charge' => $check->no_service_charge,
]);
}



public function printReceipt($id)
{
    $check = CheckEncashment::findOrFail($id);
    // Get authenticated user
     $user = auth()->user();
    
     // Get branch name
     $branchName = $user->branch ? $user->branch->name : 'Unknown';
     // Custom title rules
    if (strcasecmp($branchName, 'Guiuan') === 0) {
         $branchTitle = 'Head Office';
     } else {
         $branchTitle = $branchName . ' Branch';
     }
return view('others.check-encashments.receipt', compact('check', 'branchTitle'));
}

  
public function update(Request $request, $id)
{
    $check = CheckEncashment::find($id);

    if (!$check) {
        return response()->json(['message' => 'Record not found'], 404);
    }

    // dd($request->all());

    $validated = $request->validate([
        'transDate'       => 'sometimes|date',
        'checkNo'         => 'sometimes|string|max:50',
        'last_name'        => 'sometimes|string|max:100',
        'first_name'        => 'sometimes|string|max:100',
        'middle_name'        => 'sometimes|string|max:100',
        'bankName'        => 'sometimes|string|max:100',
        'checkAmount'     => 'sometimes|numeric',
        'charge'          => 'nullable|numeric',
        'no_service_charge' => 'nullable|boolean',
        'net_proceeds'    => 'sometimes|numeric',
        'id_type'         => 'nullable|string|max:50',
        'id_number'       => 'nullable|string|max:50',
        'id_picture'      => 'nullable|image|max:5120', // <-- file input
    ]);


    

    if ($request->boolean('no_service_charge')) {
        $validated['charge'] = 0;
        $validated['net_proceeds'] = $request->input('checkAmount');
    }

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

    return response()->json([
       'data' => $check,
       'print_url' => route('check-encashments.receipt', $check->id),
   ]);
    // return response()->json($check);
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
