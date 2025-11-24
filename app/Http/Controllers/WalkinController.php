<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Walkin;

class WalkinController extends Controller
{
 
    public function index()
    {
        $walkins = Walkin::orderBy('date', 'desc')->get();
        return view('others.walkin.index', compact('walkins'));
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
        ]);

        $walkin = Walkin::create($validated);

        return response()->json($walkin, 201);
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
    ]);
    }


    public function printReceipt($id)
    {
        $walkin = Walkin::findOrFail($id);

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

    return view('others.walkin.receipt', compact('walkin', 'branchTitle'));
        // return view('others.walkin.receipt', compact('walkin'));
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
        ]);

        $walkin->update($validated);

        return response()->json($walkin);
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
