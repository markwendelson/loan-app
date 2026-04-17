<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


//models
use App\Models\Expense;
use App\Models\ExpenseCategory;

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

    public function expenseReport()
    {
        $expenseCategories = ExpenseCategory::all();

        return view('expenses/report/index',compact('expenseCategories'));

    }

    public function getExpenseReport(Request $request) 
    {
        //get all data from ajax
        $filterData = $request->all();
        $expenseCategory = ExpenseCategory::whereIn('id', $filterData['expense_categories'])->get();



        $dateValues = [];
        //strip expense_date
        if(isset($filterData['expense_date']) && $filterData['expense_date']) {
            $expenseDateArr = explode('-', $filterData['expense_date']);

            $dateValues['from'] = date('Y-m-d', strtotime(trim($expenseDateArr[0])));
            $dateValues['to'] = date('Y-m-d', strtotime(trim($expenseDateArr[1])));
        }
        //get expenses 
        $expenses = Expense::select('expenses.expense_date', 
            'expenses.category_id',
            DB::raw('SUM(expenses.amount) AS total_amount')
            )
            ->whereIn('category_id', $filterData['expense_categories'])
            ->whereBetween('expenses.expense_date', [
                $dateValues['from'],
                $dateValues['to']
            ])
            ->groupBy('expenses.expense_date', 'expenses.category_id')
            ->orderBy('expenses.expense_date', 'ASC')
            ->get();

        $expenseData = [];
        if($expenses->isNotEmpty()) {
            foreach($expenses as $key => $val){
                $expenseData[$val->expense_date][$val->category_id] = [
                    'category_id' => $val->category_id,
                    'amount' => $val->total_amount
                ];
            }
        }

        $htmlView = view('expenses.report.report-data',compact(
            'expenseCategory',
            'dateValues',
            'expenseData'
        ))->render();

        return response()->json([
            'html' => $htmlView
        ]);

        
        





    }
}
