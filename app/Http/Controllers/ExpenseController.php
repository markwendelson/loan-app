<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use ZipArchive;

//models
use App\Models\Attachment;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\Expense;
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

        //check user if have view permission
        if(!$user->can('view-expense')) {
            abort(403, 'Unauthorized action.');
        }
        
        $branches = Branch::where('status', 1)
            ->orderBy('branch.name', 'ASC')
            ->get();

        $allowedBranches = BranchUser::where('user_id', auth()->user()->id)
            ->where('is_active', 1)
            ->pluck('branch_id')
            ->toArray();

        $categories = ExpenseCategory::all();

        //vendors
        $vendorsData = Vendor::all();

        return view('expenses.index', compact(
            'user',
            'categories',
            'branches',
            'vendorsData',
            'allowedBranches',
        ));
    }

    public function store(Request $request)
    {
        $response = [];

        //check user role if allowed to add new record
        if(!auth()->user()->can('create-expense')) {
            $response = [
                'status' => 'success',
                'message' => 'You do not have permission to create a new expense.'
            ];

            return response()->json($response);
        }

        try {
            DB::beginTransaction();

            $slug = 'EXP' . date('YmdHis');

            $insertData = [
                'branch_id' => $request->branch_id,
                'category_id' => $request->category_id,
                'payment_method' => $request->payment_method,
                'description' => $request->notes,
                'amount' => $request->total_amount,
                'expense_date' => $request->expense_date,
                'slug' => $slug,
                'created_by' => auth()->id()
            ];

            $expenseId = DB::table('expenses')->insertGetId($insertData);

            if(isset($expenseId) && $expenseId) {
                //insert expense items
                if(isset($request->vendor_id) && $request->vendor_id) {
                    $expenseItems = [];

                    foreach($request->vendor_id as $key => $val) {
                        $expenseItems[] = [
                            'expense_id' => $expenseId,
                            'vendor_id' => $request->vendor_id[$key],
                            'description' => $request->description[$key],
                            'ref_no' => $request->ref_no[$key],
                            'amount' => $request->sub_amount[$key],
                        ];
                    }

                    //insert to expense_items
                    DB::table('expense_items')->insert($expenseItems);
                }
                
                //get uploaded attachments
                if($request->hasFile('attachments')){
                    $attachments = $request->file('attachments');
                    $folderPath = 'attachments/expenses/' . $slug;

                    $directories = Storage::disk('public')->allDirectories();
                    if(!in_array($folderPath, $directories)){
                        //make directory if not yet existing
                        Storage::disk('public')->makeDirectory($folderPath);
                    }

                    $expenseAttachments = [];
                    foreach ($attachments as $val) {
                        if ($val->isValid()) {
                            //generate file name
                            $tempFileName = Str::random(40);
                            $fileExtension = $val->getClientOriginalExtension();
                            
                            $val->storeAs($folderPath, $tempFileName . '.' . $fileExtension, 'public');

                            $expenseAttachments[] = [
                                'file_reference' => 'expenses',
                                'transaction_no' => $slug,
                                'file_name' => $tempFileName . '.'. $fileExtension,
                                'file_path' => $folderPath . '/' . $tempFileName . '.' . $fileExtension,
                            ];
                        }
                    }

                    //bulk insert attachments
                    DB::table('attachments')->insert($expenseAttachments);
                }
            }
            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Expense has been successfully created.'
            ];

        } catch (\Exception $e) {
            DB::rollback();

            $repsonse = [
                'status' => 'error',
                'message' => 'There was an error while processing your request. Please try again later.'
            ];
        }

        return response()->json($response);
    }

    public function expenseEditModal(Request $request)
    {
        $inputData = $request->all(); //get all input data
        $response  = [];

        $user = auth()->user();

        $expenseData = Expense::with('expenseItems')
            ->where('id', $inputData['expense_id'])
            ->first()
            ->toArray();

        $expenseAttachments = [];
        if(isset($expenseData) && $expenseData) {
            //get expense attachments from attachments table
            $expenseAttachments = Attachment::where('transaction_no', $expenseData['slug'])->get()->toArray();
        }

        $branches = Branch::where('status', 1)
            ->orderBy('branch.name', 'ASC')
            ->get();

        $categories = ExpenseCategory::all();
        $vendorsData = Vendor::all();

        $viewHtml = view('expenses.modal.edit-expense', compact(
            'user',
            'expenseData',
            'expenseAttachments',
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
        $response = []; //response holder

        //check user if allowed to update
        if(!auth()->user()->can('edit-expense')) {
            $response = [
                'status' => 'error',
                'message' => 'You are not allowed to update expense.'
            ];
            return response()->json($response);
        }

        try {
            DB::beginTransaction();

            $inputData = $request->all(); //get all input data

            $updateData = [
                'branch_id' => $request->branch_id,
                'category_id' => $request->category_id,
                'payment_method' => $request->payment_method,
                'description' => $request->notes,
                'amount' => $request->total_amount,
                'expense_date' => $request->expense_date,
                'updated_at' => now()
            ];

            //update expenses
            $result = DB::table('expenses')->where('id', $inputData['expense_id'])->update($updateData);

            if($result) {
                //remove items / attachments that are specifically removed by user
                if(isset($inputData['deleted_items']) && $inputData['deleted_items']) {
                    $deletedItems = explode(',', $inputData['deleted_items']);
                    DB::table('expense_items')->whereIn('id', $deletedItems)->delete();
                }


                if(isset($inputData['deleted_attachments']) && $inputData['deleted_attachments']) {
                    $deletedAttachmentsIds = explode(',', $inputData['deleted_attachments']);

                    // Ensure deletedAttachmentsIds is an array
                    if (!is_array($deletedAttachmentsIds)) {
                        $deletedAttachmentsIds = array_filter(explode(',', $inputData['deleted_attachments']));
                    }

                    //get first data from database to get the folder path of the attachments
                    $forRemoveAttachments = DB::table('attachments')->whereIn('id', $deletedAttachmentsIds)->get();
                    if($forRemoveAttachments->isNotEmpty()) {
                        foreach($forRemoveAttachments as $key => $val) {
                            $filePath = $val->file_path;
                            try {
                                if(Storage::disk('public')->exists($filePath)) {
                                    Storage::disk('public')->delete($filePath);
                                }

                                DB::table('attachments')->where('id', $val->id)->delete();
                            } catch (\Exception $e) {
                                // Log the error or handle it as needed
                                Log::error("Failed to delete attachment: {$e->getMessage()}");
                            }
                        }
                    }
                }

                //update old items
                if (isset($inputData['old_vendor_id']) && $inputData['old_vendor_id']) {
                    foreach ($inputData['old_vendor_id'] as $key => $val) {
                        $itemId = $inputData['old_expense_item_id'][$key];
                        $updateItemData = [
                            'vendor_id' => $inputData['old_vendor_id'][$key],
                            'description' => $inputData['old_description'][$key],
                            'ref_no' => $inputData['old_ref_no'][$key],
                            'amount' => $inputData['old_sub_amount'][$key],
                            'updated_at' => now()
                        ];

                        try {
                            DB::table('expense_items')->where('id', $itemId)->update($updateItemData);
                        } catch (\Exception $e) {
                            // Log the error or handle it as needed
                            Log::error("Failed to update expense item with ID {$itemId}: {$e->getMessage()}");
                        }
                    }
                }

                //insert new items if any
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
                    DB::table('expense_items')->insert($expenseItems);
                }

                //get uploaded attachments
                if(isset($inputData['attachments']) && $inputData['attachments']){

                    $attachments = $inputData['attachments'];
                    $folderPath = 'attachments/expenses/' . $inputData['slug'];
                    $directories = Storage::disk('public')->allDirectories();

                    if(!in_array($folderPath, $directories)){
                        //make directory if not yet existing
                        Storage::disk('public')->makeDirectory($folderPath);
                    }

                    $expenseAttachments = [];
                    foreach ($attachments as $val) {
                        if ($val->isValid()) {
                            //generate file name
                            $tempFileName = Str::random(40);
                            $fileExtension = $val->getClientOriginalExtension();
                            
                            $val->storeAs($folderPath, $tempFileName . '.' . $fileExtension , 'public');

                            $expenseAttachments[] = [
                                'file_reference' => 'expenses',
                                'transaction_no' => $inputData['slug'],
                                'file_name' => $tempFileName . '.'. $fileExtension,
                                'file_path' => $folderPath . '/' . $tempFileName . '.' . $fileExtension,
                            ];
                        }
                    }

                    //bulk insert attachments
                    DB::table('attachments')->insert($expenseAttachments);
                }
                
            }

            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Expense updated successfully.'
            ];

        } catch (\Exception $e) {
            //handle exception here
            DB::rollback();

            $response = [
                'status' => 'error',
                'message' => 'Failed to update expenses. Please try again later.'
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

        $expenseAttachments = [];
        if(isset($expenseData) && $expenseData) {
            //get expense attachments from attachments table
            $expenseAttachments = Attachment::where('transaction_no', $expenseData['slug'])->get()->toArray();
        }
        
        $viewHtml = view('expenses.modal.view-expense', compact(
            'expenseData',
            'expenseAttachments'
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

    public function downloadExpenseDetailsPdf(Request $request)
    {
        
        $expenseData = Expense::with('expenseItems')
            ->select(
                'expenses.*',
                'branch.name AS branch_name',
                'expense_categories.name AS category_name',
                'users.name AS user_name'
            )
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.category_id')
            ->leftJoin('branch', 'branch.id', '=', 'expenses.branch_id')
            ->leftJoin('users', 'users.id', '=', 'expenses.created_by')
            ->where('expenses.slug', $request->slug)
            ->first()
            ->toArray();

        $pdf = Pdf::loadView('expenses.report.download-expense-details-pdf', compact(
            'expenseData'
        ))->setPaper('a4', 'portrait');

        return $pdf->download($expenseData['slug'] . '.pdf');
    }

    public function downloadExpenseDetailsImages(Request $request)
    {
        $attachmentsData = Attachment::where('file_reference', 'expenses')
            ->where('transaction_no', $request->slug)
            ->get();

        $files = [];

        if ($attachmentsData->isNotEmpty()) {
            foreach ($attachmentsData as $val) {
                // FIX 1: no nested array
                $files[] = $val->file_path;
            }
        }

        $zipName = $request->slug . '.zip';
        $zipPath = storage_path("app/public/{$zipName}");

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            foreach ($files as $file) {

                // FIX 2: correct full path
                $filePath = storage_path("app/public/" . $file);

                if (file_exists($filePath)) {
                    // FIX 3: ensure basename uses string only
                    $zip->addFile($filePath, basename($file));
                }
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function generateExpenseReport(Request $request)
    {
        $inputData = $request->all();
        $reportType = $inputData['report_type'];
        $reportTypeDate = '';
        $reportTypeYear = '';

        $query = ExpenseItems::select(
                'expense_items.id',
                'expense_items.expense_id',
                'expense_items.description',
                'expense_items.ref_no',
                'expense_items.amount',
                'expense_items.created_at',
                'vendors.name as vendor_name',
                'vendors.tin',
                'vendors.tax_classification',
                'branch.name as branch_name',
                'expense_categories.name AS category_name',
            )
            ->leftJoin('expenses', 'expenses.id', '=', 'expense_items.expense_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.category_id')
            ->leftJoin('branch', 'branch.id', '=', 'expenses.branch_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'expense_items.vendor_id')
            ->whereIn('expenses.branch_id', $inputData['branch']);

        switch ($inputData['report_type']) {
            case 'daily':
                $reportTypeDate = date('M d, Y', strtotime($inputData['daily']['date']));
                $reportTypeYear = date('Y', strtotime($inputData['daily']['date']));

                $query->where('expenses.branch_id', auth()->user()->current_branch_id)
                    ->where('expenses.expense_date', $inputData['daily']['date']);
                break;
            case 'monthly':
                $reportTypeDate = date('M d', strtotime($inputData['monthly']['month_from'])) . ' - ' . date('M d', strtotime($inputData['monthly']['month_to']));

                $monthFromYear = date('Y', strtotime($inputData['monthly']['month_from']));
                $monthToYear = date('Y', strtotime($inputData['monthly']['month_to']));


                if($monthFromYear === $monthToYear) {
                    $reportTypeYear = $monthFromYear;

                }else{
                    $reportTypeYear = $monthFromYear . '-' . $monthToYear;
                }

                $query->whereBetween('created_at', date('Y-m-d', strtotime($inputData['monthly']['month_from'])), date('Y-m-d', strtotime($inputData['monthly']['month_to'])));
            break;
            case 'quarterly':
                $reportTypeDate = config('constants.quarter_value')[$inputData['quarterly']['quarter']];
                $reportTypeYear = $inputData['quarterly']['year'];

                $from = config('constants.quarter_date_value')[$inputData['quarterly']['quarter']]['from'] . ' ' . $inputData['quarterly']['year'];
                $to = config('constants.quarter_date_value')[$inputData['quarterly']['quarter']]['to'] . ' ' . $inputData['quarterly']['year'];
                
                $query->whereBetween('expense_items.created_at', [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))]);
            break;

            case 'yearly':
                $reportTypeYear = $inputData['yearly']['year'];

                $query->whereYear('expense_items.created_at', '=', $reportTypeYear);
            break;
        }
        $reportData = $query->get();

        return Pdf::loadView('expenses.report.daily-report', compact(
                'reportData',
                'reportType',
                'reportTypeDate',
                'reportTypeYear',
            ))
            ->setPaper('legal', 'landscape')
            ->stream('daily-report.pdf');
    }

    public function deleteExpense(Request $request) 
    {
        //response
        $response = [];

        if(!auth()->user()->can('delete-expense')) {
            $response = [
                'status' => 'error',
                'message' => 'You do not have permission to delete this expense.'
            ];

            return response()->json($response);
        }

        if($request->has('expense_id')) {
            try {

                DB::beginTransaction();
                Expense::where('id', $request->expense_id)->delete();
                DB::commit();

                $response = [
                    'status' => 'success',
                    'message' => 'Expense deleted successfully.'
                ];
            } catch (\Exception $e) {
                DB::rollback();
                Log::error("Failed to delete expense: {$e->getMessage()}");

                $response = [
                    'status' => 'error',
                    'message' => 'Error deleting expense.'
                ];
            }
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
        //temp return to dashboard
        return redirect('/');

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

    // public function generateExpenseReport(Request $request) 
    // {
    //     $inputData = $request->all();
        
    //     $validator = Validator::make($request->all(), [
    //         'expense_date' => 'required',
    //         'expense_categories' => 'required|array',
    //     ]);

    //     if($validator->fails()){
    //         $allErrors = implode(',', $validator->errors()->all());
    //         $response = [
    //             'status' => 'fail',
    //             'message' => 'Validation Failed!: ' . $allErrors,
    //         ];

    //         return response()->json($response);
    //     }

    //     try {
    //         $dateValues = [];
    //         //strip expense_date
    //         if(isset($inputData['expense_date']) && $inputData['expense_date']) {
    //             $expenseDateArr = explode('-', $inputData['expense_date']);

    //             $dateValues['from'] = date('Y-m-d', strtotime(trim($expenseDateArr[0])));
    //             $dateValues['to'] = date('Y-m-d', strtotime(trim($expenseDateArr[1])));
    //         }

    //         //get expenses 
    //         $expenses = Expense::select('expenses.expense_date', 
    //             'expenses.category_id',
    //             DB::raw('SUM(expenses.amount) AS total_amount')
    //             )
    //             ->whereIn('category_id', $inputData['expense_categories'])
    //             ->whereBetween('expenses.expense_date', [
    //                 $dateValues['from'],
    //                 $dateValues['to']
    //             ])
    //             ->groupBy('expenses.expense_date', 'expenses.category_id')
    //             ->orderBy('expenses.expense_date', 'ASC')
    //             ->get();

    //         $expenseData = [];
    //         $totalAmount = 0;
    //         if($expenses->isNotEmpty()) {
    //             foreach($expenses as $key => $val){
    //                 $totalAmount += (double)$val->total_amount;
    //                 $expenseData[$val->expense_date][$val->category_id] = [
    //                     'category_id' =>$val->category_id,
    //                     'amount' => $val->total_amount
    //                 ];
    //             }
    //         }

    //         ExpenseReport::create([
    //             'user_id' => auth()->id(),
    //             'date_from' => $dateValues['from'],
    //             'date_to' => $dateValues['to'],
    //             'description' => $inputData['description'],
    //             'categories_data' => json_encode($inputData['expense_categories']),
    //             'amount' => (double)$totalAmount,
    //             'data' => json_encode($expenseData),
    //             'slug' => \Str::slug('exp' . date('Ymd H:i:s'))
    //         ]);

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Expense report generated successfully.'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Error saving expense report: ' . $e->getMessage()
    //         ], 500);
    //     }

        
    // }

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
