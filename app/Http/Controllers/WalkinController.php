<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Walkin;

class WalkinController extends Controller
{
    public function index()
    {
        $walkins = Walkin::orderBy('id', 'desc')->get();
        return view('others.walkin.index', compact('walkins'));
    }

    public function getAll()
    {
        $walkins = Walkin::orderBy('date', 'desc')->get();
        return response()->json($walkins);
    }

    public function store(Request $request)
    {
        \Log::info('STORE reached', $request->all());
       dd('Store controller reached', $request->all());

        $request->merge($request->json()->all());

        $validated = $request->validate([
            'date' => 'required|date',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:5',
            'amount_withdrawn' => 'required|numeric',
            'charge' => 'required|numeric',
            'net_proceeds' => 'required|numeric',
        ]);

        // CHECK IF ALREADY EXISTS 
        $existing = Walkin::where('date', $validated['date'])
            ->where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This walk-in record already exists.',
                'existing_record' => $existing
            ], 409); 
        }

        // Save new walk-in
        $walkin = Walkin::create($validated);

        return response()->json(['success' => true, 'data' => $walkin]);
    }

    public function update(Request $request, Walkin $walkin)
    {
        $request->merge($request->json()->all());

        $validated = $request->validate([
            'date' => 'required|date',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:5',
            'amount_withdrawn' => 'required|numeric',
            'charge' => 'required|numeric',
            'net_proceeds' => 'required|numeric',
        ]);

        $walkin->update($validated);

        return response()->json(['success' => true, 'data' => $walkin]);
    }
}
