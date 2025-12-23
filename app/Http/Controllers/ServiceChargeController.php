<?php

namespace App\Http\Controllers; 

use App\Models\ServiceCharge;
use Illuminate\Http\Request;

class ServiceChargeController extends Controller
{
    public function index()
    {
        $fees = ServiceCharge::orderByDesc('id')->get();
        return view('service-charge.index', compact('fees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'percentage' => 'required|numeric|min:0',
            'fee_type' => 'required|in:walkin,check',
            'is_active' => 'required|boolean',
        ]);

        ServiceCharge::create($validated);

        return response()->json(['message' => 'Service fee added']);
    }

    public function show($id)
    {
        return ServiceCharge::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $fee = ServiceCharge::findOrFail($id);

        $validated = $request->validate([
            'percentage' => 'required|numeric|min:0',
            'fee_type' => 'required|in:walkin,check',
            'is_active' => 'required|boolean',
        ]);

        $fee->update($validated);

        return response()->json(['message' => 'Service fee updated']);
    }

    public function destroy($id)
    {
        ServiceCharge::findOrFail($id)->delete();
        return response()->json(['message' => 'Service fee deleted']);
    }
}