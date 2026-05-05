<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


//models
use App\Models\Expense;
use App\Models\ExpenseReport;
use App\Models\ExpenseCategory;
use App\Models\User;

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

    public function expenseReport(Request $request)
    {
        if(!auth()->user()->can('view-expense-report')) {
            abort(403, 'Unauthorized action.');
        }

        $filterData = $request->all();
        
        $users = User::where('status', 1)->orderBy('name', 'ASC')->get();

        $expenseCategories = ExpenseCategory::where('status', 1)->orderBy('expense_categories.name', 'ASC')
            ->get();
        $expenseReports = ExpenseReport::select('expense_reports.*', 'users.name as user_name')
            ->orderBy('created_at', 'DESC')
            ->leftJoin('users', 'users.id', '=', 'expense_reports.user_id');

        //filter users
        if (isset($filterData['users']) && $filterData['users']) {
            $expenseReports->whereIn('expense_reports.user_id', $filterData['users']);
        }

        //filter date created
        if(isset($filterData['date_generated']) && $filterData['date_generated']) {
            $dateCreatedArr = explode('-', $filterData['date_generated']);
            $from = date('Y-m-d', strtotime(trim($dateCreatedArr[0])));
            $to = date('Y-m-d', strtotime(trim($dateCreatedArr[1])));

            $expenseReports->whereBetween('expense_reports.created_at', [$from, $to]);
        }

        $expenseReports = $expenseReports->paginate(10);

        $expenseReports->appends($filterData);
            
        return view('expenses/report/index',compact(
            'expenseCategories',
            'expenseReports',
            'users',
            'filterData'
            ));
    }

    public function generateExpenseReport(Request $request) 
    {
        $inputData = $request->all();
        
        $validator = Validator::make($request->all(), [
            'expense_date' => 'required',
            'expense_categories' => 'required|array',
        ]);

        if($validator->fails()){
            $allErrors = implode(',', $validator->errors()->all());
            $response = [
                'status' => 'fail',
                'message' => 'Validation Failed!: ' . $allErrors,
            ];

            return response()->json($response);
        }

        try {
            $dateValues = [];
            //strip expense_date
            if(isset($inputData['expense_date']) && $inputData['expense_date']) {
                $expenseDateArr = explode('-', $inputData['expense_date']);

                $dateValues['from'] = date('Y-m-d', strtotime(trim($expenseDateArr[0])));
                $dateValues['to'] = date('Y-m-d', strtotime(trim($expenseDateArr[1])));
            }

            //get expenses 
            $expenses = Expense::select('expenses.expense_date', 
                'expenses.category_id',
                DB::raw('SUM(expenses.amount) AS total_amount')
                )
                ->whereIn('category_id', $inputData['expense_categories'])
                ->whereBetween('expenses.expense_date', [
                    $dateValues['from'],
                    $dateValues['to']
                ])
                ->groupBy('expenses.expense_date', 'expenses.category_id')
                ->orderBy('expenses.expense_date', 'ASC')
                ->get();

            $expenseData = [];
            $totalAmount = 0;
            if($expenses->isNotEmpty()) {
                foreach($expenses as $key => $val){
                    $totalAmount += (double)$val->total_amount;
                    $expenseData[$val->expense_date][$val->category_id] = [
                        'category_id' =>$val->category_id,
                        'amount' => $val->total_amount
                    ];
                }
            }

            ExpenseReport::create([
                'user_id' => auth()->id(),
                'date_from' => $dateValues['from'],
                'date_to' => $dateValues['to'],
                'description' => $inputData['description'],
                'categories_data' => json_encode($inputData['expense_categories']),
                'amount' => (double)$totalAmount,
                'data' => json_encode($expenseData),
                'slug' => \Str::slug('exp' . date('Ymd H:i:s'))
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Expense report generated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving expense report: ' . $e->getMessage()
            ], 500);
        }

        
    }

    public function viewExpenseReport($slug)
    {
        $expenseReport = ExpenseReport::select('expense_reports.*', 'users.id as user_id', 'users.name as user_name')
            ->where('slug', $slug)
            ->leftJoin('users', 'users.id', 'expense_reports.user_id')
            ->firstOrFail();
        $expenseCategories = ExpenseCategory::whereIn('id', json_decode($expenseReport->categories_data))->get()->keyBy('id');

        return view('expenses.report.report-data', compact(
            'expenseReport',
            'expenseCategories'
        ));

    }

    public function downloadPdf($slug)
    {
        $expenseReport = ExpenseReport::select('expense_reports.*', 'users.id as user_id', 'users.name as user_name')
            ->where('slug', $slug)
            ->leftJoin('users', 'users.id', 'expense_reports.user_id')
            ->firstOrFail();
        $expenseCategories = ExpenseCategory::whereIn('id', json_decode($expenseReport->categories_data))->get()->keyBy('id');

        $pdf = Pdf::loadView('expenses.report.report-data-pdf', compact(
            'expenseReport',
            'expenseCategories'
        ))->setPaper('a4', 'landscape');

        return $pdf->download($slug . '.pdf');
    }
}
