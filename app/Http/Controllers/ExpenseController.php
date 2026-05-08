<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

//models
use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseAttachments;
use App\Models\ExpenseItems;
use App\Models\ExpenseReport;
use App\Models\ExpenseCategory;
use App\Models\User;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::select('expenses.*', 
                'branch.name AS branch_name', 
                'users.name AS user_name', 
                'users.email'
            )
            ->leftJoin('branch', 'branch.id' , '=', 'expenses.branch_id')
            ->leftJoin('users', 'users.id', '=', 'expenses.created_by')
            ->orderBy('expenses.expense_date', 'DESC')
            ->paginate(10);

        // dd($expenses->toArray());
        $branches = Branch::where('status', 1)
            ->orderBy('branch.name', 'ASC')
            ->get();
        $categories = ExpenseCategory::all();

        return view('expenses.index', compact(
            'expenses',
            'categories',
            'branches'
        ));
    }

    public function store(Request $request)
    {
        $slug = 'EXP' . date('YmdHis');

        $result = Expense::create([
            'branch_id' => $request->branch_id,
            'vendor_name' => $request->vendor_name,
            'ref_no' => $request->reference_no,
            'payment_method' => $request->payment_method,
            'tax_rate' => $request->tax_rate,
            'sub_total' => $request->sub_amount,
            'amount' => $request->amount,
            'tax' => $request->tax,
            'description' => $request->notes,
            'expense_date' => $request->expense_date,
            'slug' => $slug,
            'created_by' => auth()->id()
        ]);

        if(isset($result) && $result) {
            //insert expense items
            if(isset($request->category_id) && $request->category_id) {
                $expenseItems = [];

                foreach($request->category_id as $key => $val) {
                    $expenseItems[] = [
                        'expense_id' => $result->id,
                        'category_id' => $val,
                        'description' => $request->description[$key],
                        'qty' => $request->qty[$key],
                        'unit_price' => $request->unit_price[$key],
                        'sub_total' => $request->sub_total[$key],
                    ];
                }

                //insert to expense_items
                ExpenseItems::insert($expenseItems);
            }

            //get uploaded attachments
            if($request->hasFile('attachments')){
                $attachments = $request->file('attachments');
                $folderPath = 'expenses/attachments/' . $slug;

                $directories = Storage::disk('public')->allDirectories();
                if(!in_array($folderPath, $directories)){
                    //make directory if not yet existing
                    Storage::disk('public')->makeDirectory($folderPath);
                }

                $expenseAttachments = [];
                foreach ($attachments as $val) {
                    if ($val->isValid()) {
                        //generate file name
                        $tempFileName = Str::random(40) . '.' . $val->getClientOriginalExtension();
                        
                        $val->storeAs($folderPath, $tempFileName, 'public');

                        $expenseAttachments[] = [
                            'expense_id' => $result->id,
                            'name' => $tempFileName
                        ];
                    }
                }

                //bulk insert attachments
                ExpenseAttachments::insert($expenseAttachments);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully added new expense'
        ]);
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
