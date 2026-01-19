<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = \App\Models\Expense::with(['category', 'creator'])->latest('expense_date')->get();
        $categories = \App\Models\ExpenseCategory::all();
        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        \App\Models\Expense::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Expense create successfully']);
        }

        return redirect()->route('expenses.index')->with('message', 'Expense created successfully');
    }

    public function show($id)
    {
        $expense = \App\Models\Expense::findOrFail($id);
        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $expense = \App\Models\Expense::findOrFail($id);
        $expense->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Expense updated successfully']);
        }

        return redirect()->route('expenses.index')->with('message', 'Expense updated successfully');
    }

    public function destroy($id)
    {
        $expense = \App\Models\Expense::findOrFail($id);
        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }
}
