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
use App\Models\ExpenseCategory;
use App\Models\ExpenseItems;
use App\Models\ExpenseReport;
use App\Models\User;
use App\Models\Vendor;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        //filter data
        $filterData = $request->all();

        //check user if have view permission
        if(!$user->can('view-expense')) {
            abort(403, 'Unauthorized action.');
        }
        $query = Expense::select('expenses.*', 
                'branch.name AS branch_name', 
                'users.name AS user_name', 
                'users.email'
            )
            ->leftJoin('branch', 'branch.id' , '=', 'expenses.branch_id')
            ->leftJoin('users', 'users.id', '=', 'expenses.created_by')
            ->orderBy('expenses.expense_date', 'DESC');
        //branch_id
        if(isset($request->branch_id) && $request->branch_id){
            $query->where('expenses.branch_id', $request->branch_id);
        }

        //author
        if(isset($request->author) && $request->author) {
            $query->where('expenses.created_by', $request->author);
        }

        $expenses = $query->paginate(10)->appends($request->all());

        // dd($expenses->toArray());
        $branches = Branch::where('status', 1)
            ->orderBy('branch.name', 'ASC')
            ->get();
        $categories = ExpenseCategory::all();

        //users
        $usersData = User::withTrashed()
        ->where('status', 1)->get();

        //vendors
        $vendorsData = Vendor::all();

        return view('expenses.index', compact(
            'user',
            'usersData',
            'expenses',
            'categories',
            'branches',
            'vendorsData',
            'filterData'
        ));
    }

    public function store(Request $request)
    {
        $slug = 'EXP' . date('YmdHis');

        $insertData = [
            'branch_id' => $request->branch_id,
            'payment_method' => $request->payment_method,
            'description' => $request->notes,
            'amount' => $request->total_amount,
            'expense_date' => $request->expense_date,
            'slug' => $slug,
            'created_by' => auth()->id()
        ];

        $result = Expense::create($insertData);

        if(isset($result) && $result) {
            //insert expense items
            if(isset($request->vendor_id) && $request->vendor_id) {
                $expenseItems = [];

                foreach($request->vendor_id as $key => $val) {
                    $expenseItems[] = [
                        'expense_id' => $result->id,
                        'vendor_id' => $request->vendor_id[$key],
                        'description' => $request->description[$key],
                        'ref_no' => $request->ref_no[$key],
                        'amount' => $request->sub_amount[$key],
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

    public function expenseEditModal(Request $request)
    {
        $inputData = $request->all(); //get all input data
        $response  = [];

        $user = auth()->user();

        $expenseData = Expense::with('expenseItems', 'expenseAttachments')
            ->where('id', $inputData['expense_id'])
            ->first()
            ->toArray();

        $branches = Branch::where('status', 1)
            ->orderBy('branch.name', 'ASC')
            ->get();

        $categories = ExpenseCategory::all();
        $vendorsData = Vendor::all();

        $viewHtml = view('expenses.modal.edit-expense', compact(
            'user',
            'expenseData',
            'categories',
            'branches',
            'vendorsData',
        ))->render();
        
        if(!empty($viewHtml)) {
            $response = [
                'status' => 'success',
                'html' => $viewHtml
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error loading expense data.'
            ];
        }

        return response()->json($response);
    }

    public function updateExpenses(Request $request)
    {
        $inputData = $request->all(); //get all input data
        $response = []; //response holder

        // dd($inputData);

        if(isset($inputData) && empty($inputData)) {
            $response = [
                'status' => 'error',
                'message' => 'No data provided for update.'
            ];

            return response()->json($response);
        }

        $updateData = [
            'branch_id' => $request->branch_id,
            'payment_method' => $request->payment_method,
            'description' => $request->notes,
            'amount' => $request->total_amount,
            'expense_date' => $request->expense_date,
            'updated_at' => now()
        ];
        //update expense data
        $result = Expense::find($inputData['expense_id'])
            ->update($updateData);

        if($result) {
            //remove items / attachments that are specifically removed by user
            if(isset($inputData['deleted_items']) && $inputData['deleted_items']) {
                $deletedItems = explode(',', $inputData['deleted_items']);
                ExpenseItems::whereIn('id', $deletedItems)->delete();
            }

            if(isset($inputData['deleted_attachments']) && $inputData['deleted_attachments']) {
                $deletedAttachmentsIds = explode(',', $inputData['deleted_attachments']);

                //get first data from database to get the folder path of the attachments
                $forRemoveAttachments = ExpenseAttachments::whereIn('id', $deletedAttachmentsIds)->get();
                if($forRemoveAttachments->isNotEmpty()) {
                    foreach($forRemoveAttachments as $key => $val) {
                        $filePath = 'expenses/attachments/' . $inputData['slug'] . '/' . $val->name;
                        if(Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                    }
                }

                ExpenseAttachments::whereIn('id', $deletedAttachmentsIds)->delete();
            }

            //update old items
            if(isset($inputData['old_vendor_id']) && $inputData['old_vendor_id']) {
                foreach($inputData['old_vendor_id'] as $key => $val) {
                    $itemId = $inputData['old_expense_item_id'][$key];
                    $updateItemData = [
                        'vendor_id' => $inputData['old_vendor_id'][$key],
                        'description' => $inputData['old_description'][$key],
                        'ref_no' => $inputData['old_ref_no'][$key],
                        'amount' => $inputData['old_sub_amount'][$key],
                        'updated_at' => now()
                    ];

                    ExpenseItems::where('id', $itemId)->update($updateItemData);
                }
            }

            if(isset($inputData['vendor_id']) && $inputData['vendor_id']) {
                $expenseItems = [];

                foreach($inputData['vendor_id'] as $key => $val) {
                    $expenseItems[] = [
                        'expense_id' => $inputData['expense_id'],
                        'vendor_id' => $inputData['vendor_id'][$key],
                        'description' => $inputData['description'][$key],
                        'ref_no' => $inputData['ref_no'][$key],
                        'amount' => $inputData['sub_amount'][$key],
                    ];
                }

                //insert to expense_items
                ExpenseItems::insert($expenseItems);
            }

            //get uploaded attachments
            if($inputData['attachments']){
                $attachments = $inputData['attachments'];
                $folderPath = 'expenses/attachments/' . $inputData['slug'];

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
                            'expense_id' => $inputData['expense_id'],
                            'name' => $tempFileName
                        ];
                    }
                }

                //bulk insert attachments
                ExpenseAttachments::insert($expenseAttachments);
            }

            $response = [
                'status' => 'success',
                'message' => 'Expense updated successfully.'
            ];
        }

        return response()->json($response);
    }

    public function viewExpenseData(Request $request)
    {
        $response = []; //response holder

        $inputData = $request->all(); //get all input data

        $expenseData = Expense::with('expenseItems', 'expenseAttachments')
            ->select(
                'expenses.*',
                'branch.name AS branch_name',
                'expense_categories.name AS category_name',
                'users.name AS user_name'
            )
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.category_id')
            ->leftJoin('branch', 'branch.id', '=', 'expenses.branch_id')
            ->leftJoin('users', 'users.id', '=', 'expenses.created_by')
            ->where('expenses.id', $inputData['expense_id'])
            ->first()
            ->toArray();
        
        $viewHtml = view('expenses.modal.view-expense', compact(
            'expenseData'
        ))->render();

        if(!empty($viewHtml) && $expenseData) {
            $response = [
                'status' => 'success',
                'html' => $viewHtml
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error loading expense data.'
            ];
        }

        return response()->json($response);
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
