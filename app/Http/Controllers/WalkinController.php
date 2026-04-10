<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Walkin;

class WalkinController extends Controller
{

    public function index()
    {
        $serviceCharge = DB::table('service_charges')
        ->where('is_active', 1)
        ->where('fee_type', 'walkin')   
        ->orderByDesc('id')             
        ->value('percentage') ?? 0;

         // Fetch existing walkins
        $walkins = Walkin::orderBy('date', 'desc')->get();
        //  dd($serviceCharge);

             // 'date', 'desc')->get();
        return view('others.walkin.index', compact('walkins', 'serviceCharge'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_initial' => 'nullable|string|max:5',
            'amount_withdrawn' => 'required|numeric',
            'charge' => 'nullable|numeric',
            'net_proceeds' => 'required|numeric',
            'no_service_charge' => 'nullable|boolean',
        ]);

    if ($request->no_service_charge) {
        $validated['charge'] = 0;
        $validated['net_proceeds'] = $validated['amount_withdrawn'];

    }

        $walkin = Walkin::create($validated);

          return response()->json([
            'data' => $walkin,
            'print_url' => route('walkin.receipt', $walkin->id),
        ], 201);
   
    }

    public function show($id)
    {
        $walkin = Walkin::find($id);
        if (!$walkin) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        // Format date for modal (YYYY-MM-DD)
        $walkin->date = $walkin->date->format('Y-m-d');

           $walkin = Walkin::findOrFail($id);

    return response()->json([
        'id' => $walkin->id,
        'date' => $walkin->date->format('Y-m-d'), // <- safe for <input type="date">
        'last_name' => $walkin->last_name,
        'first_name' => $walkin->first_name,
        'middle_initial' => $walkin->middle_initial,
        'amount_withdrawn' => $walkin->amount_withdrawn,
        'charge' => $walkin->charge,
        'net_proceeds' => $walkin->net_proceeds,
        'no_service_charge' => $walkin->no_service_charge,
    ]);


    }


    public function printReceipt($id)
    {
         $walkin = Walkin::findOrFail($id);

         $user = auth()->user();
         $branchName = $user?->branch?->name ?? 'Unknown';

         $branchTitle = strcasecmp($branchName, 'Guiuan') === 0
             ? 'Head Office'
             : $branchName . ' Branch';

         return view('others.walkin.receipt', compact('walkin', 'branchTitle'));
    }

   
    public function update(Request $request, $id)
    {
        $walkin = Walkin::find($id);
        if (!$walkin) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_initial' => 'nullable|string|max:5',
            'amount_withdrawn' => 'required|numeric',
            'charge' => 'nullable|numeric',
            'net_proceeds' => 'required|numeric',
            'no_service_charge' => 'nullable|in:0,1',
        ]);

        if ($request->boolean('no_service_charge')) {
            $validated['charge'] = 0;
            $validated['net_proceeds'] = $validated['amount_withdrawn'];
        }
        $walkin->update($validated);

        return response()->json([
            'data' => $walkin,
            'print_url' => route('walkin.receipt', $walkin->id),
        ]);

    }


    public function destroy($id)
    {
        $walkin = Walkin::find($id);
        if (!$walkin) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $walkin->delete();

        return response()->json(['message' => 'Record deleted successfully']);
    }
}
