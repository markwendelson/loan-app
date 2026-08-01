<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientGroupsController;
use App\Http\Controllers\ClientIDsController;
use App\Http\Controllers\DepartmentController;
// use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\InsuranceFeeScheduleController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanProductController;
use App\Http\Controllers\LoanTypeController;
use App\Http\Controllers\NotaryFeeScheduleController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CheckEncashmentController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
// ----- Others
use App\Http\Controllers\WalkinController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\ServiceChargeController;
use App\Http\Controllers\DocumentTypeController;

Route::get('/', function () {
    return redirect('/dashboard');
});


Route::get('/logout', function () {
    Auth::logout();

    return redirect('/login');
})->name('custom.logout');

Auth::routes();

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard/metric', [HomeController::class, 'getMetric'])->name('dashboard.metric');

    Route::post('cities/get-cities', [CityController::class, 'getCitiesHtmlOptions']);

    // Route::any('client-registration', [ClientController::class, 'index']);
    // Route::get('client-list', [ClientController::class, 'clientList']);
    // Route::any('client-edit/{id}', [ClientController::class, 'clientEdit']);

    Route::post('upload-valid-id', [ClientIDsController::class, 'uploadValidID'])->name('uploadValidID');

    //-----Employee
    // Route::get('employee-list', [EmployeeController::class, 'index'])->name('employee.index');
    // Route::any('employee-registration', [EmployeeController::class, 'register'])->name('employee.register');
    // Route::match(['get', 'post'],'employee-update/{id}', [EmployeeController::class, 'update'])->name('employee.update');

    //-----Branches
    Route::get('branches', [BranchController::class, 'index'])->name('branch.index');
    Route::delete('branches/{id}', [BranchController::class, 'destroy'])->name('branch.destroy');
    Route::put('branches/{id}', [BranchController::class, 'update'])->name('branch.update');
    Route::get('branches/{id}', [BranchController::class, 'show'])->name('branch.show');
    Route::post('branches', [BranchController::class, 'store'])->name('branch.store');

    //-----Department
    Route::get('departments', [DepartmentController::class, 'index'])->name('department.index');
    Route::post('department/get-department', [DepartmentController::class, 'getDepartmentInfo']);
    Route::post('departments/submit-form', [DepartmentController::class, 'submitDepartment']);

    //-----JobTitle
    Route::get('job-titles', [JobTitleController::class, 'index'])->name('job-title.index');
    Route::post('job-titles', [JobTitleController::class, 'store'])->name('job-title.store');
    Route::get('job-titles/{id}', [JobTitleController::class, 'show'])->name('job-title.show');
    Route::put('job-titles/{id}', [JobTitleController::class, 'update'])->name('job-title.update');
    Route::delete('job-titles/{id}', [JobTitleController::class, 'destroy'])->name('job-title.destroy');

    //-----Insurance Fee Schedule
    // Route::get('insurance-fee', [InsuranceFeeController::class, 'index'])->name('insurance_fee.index');
    // Route::post('insurance-fee/get-insurance-fee-schedule', [InsuranceFeeController::class, 'getInsuranceFeeInfo']);
    // Route::post('insurance-fee/submit-form', [InsuranceFeeController::class, 'submitInsFeeSched']);

    //-----Notary Fee Schedule
    // Route::get('notary-fee', [NotaryFeeController::class, 'index'])->name('notary.index');
    // Route::post('notary-fee/get-notary-fee-schedule', [NotaryFeeController::class, 'getNotaryFeeInfo']);
    // Route::post('notary-fee/submit-form', [NotaryFeeController::class, 'submitNotarySched']);

    Route::get('/notifications', [NotificationsController::class, 'showNotifications'])->name('notifications.index');
    Route::post('/mark-notifications-read', [NotificationsController::class, 'markNotificationsAsRead'])->name('notifications.markAsRead');

    Route::get('/get-insurance-fee', [InsuranceFeeScheduleController::class, 'getInsuranceFee'])->name('get_insurance_fee');
    Route::get('/get-notary-fee', [NotaryFeeScheduleController::class, 'getNotaryFee'])->name('get_notary_fee');
    Route::get('/get-fees', [SavingsController::class, 'getFees'])->name('get_fees');
    Route::get('/get-fees-by-month-and-year', [SavingsController::class, 'getFeesByMonthAndYear'])->name('get_fees_by_month_and_year');

    Route::controller(ClientController::class)
        ->prefix('client')
        ->group(function () {
            Route::get('registration', 'create')->name("client.registration");
            Route::get('{client}/get-balance', 'getBalance')->name('client.get-balance');
            Route::get('{client}/{loan_no}/ledger', 'ledgerLoan')->name('client.ledger.loan');
            Route::get('{client}/loan-history', 'loanHistory')->name('client.loan-history');
            Route::get('{client}/ledger', 'ledgerPrincipal')->name('client.ledger.principal');
            Route::get('{client}/ledger/special', 'ledgerSpecial')->name('client.ledger.special');
            Route::get('{client}/loan-history-details', 'loanHistoryDetails')->name('client.loan-history-details');
            Route::get('{client}/partial-ledger', 'partialLedger')->name('client.partial-ledger');
            Route::get('{id}/get-co-makers', 'getCoMaker')->name('client.get_co_makers');
        });

        Route::get('user/{user}/branch/{branch}/permission', [UserController::class, 'getBranchPermission'])->name('user.get-branch-permission');
        Route::post('user/{user}/branch/{branch}/permission', [UserController::class, 'updateBranchPermission'])->name('user.update-branch-permission');
        Route::post('user/{user}/permission', [UserController::class, 'updatePermission'])->name('user.update-permission');
        Route::post('user/{user}/role',[UserController::class, 'assignRole'])->name('user.assign-role');
        Route::put('user/reset-password', [UserController::class, 'resetPassword'])->name('user.reset-password');

        Route::delete('user/{user}/branch/{branchId}', [UserController::class, 'deleteBranchRole'])->name('user.delete-branch-role');

        Route::post('user/update-branch', [UserController::class, 'updateUserBranch'])->name('user.update-branch');

    Route::controller(ClientGroupsController::class)
        ->prefix('client-group')
        ->group(function() {
            Route::any('/', 'index')->name('client-group.index');
            Route::get('types', 'clientGrouptypes')->name('client-group-types.index');
            Route::delete('remove-grouping/{id}', 'removeClientGrouping')->name('client-group.remove-client-grouping');
            Route::post('store-group-types', 'storeClientGroupTypes')->name('client-group.store');
            Route::post('get-group-types', 'getGroupType')->name('client-group-types.get-types');
            Route::put('put-group-types', 'putClientGroupTypes')->name('put-group-types.put');
    });

    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class);
    Route::resource('client', ClientController::class);
    Route::resource('insurance-fee-schedule', InsuranceFeeScheduleController::class);
    Route::resource('notary-fee-schedule', NotaryFeeScheduleController::class);
    Route::controller(LoanController::class)
        ->prefix('loan')
        ->group(function () {
            Route::post('{id}/request-loan-withdraw', 'requestLoanWithraw')->name('loan.reques-withdraw');
            Route::get('pending', 'pending')->name('loan.pending');
            Route::get('awaiting-disbursement', 'awaitingDisbursement')->name('loan.awaiting-disbursement');
            Route::get('declined', 'declined')->name('loan.declined');
            Route::get('withdrawn', 'withdrawn')->name('loan.withdrawn');
            Route::get('closed', 'closed')->name('loan.closed');
            Route::post('disburse/{loan}', 'disburse')->name('loan.disburse');
            Route::post('withdrawn/{loan}', 'withdrawnLoan')->name('loan.withdrawn-loan');
            Route::post('approve/{loan}', 'approveLoan')->name('loan.approve-loan');
            Route::post('decline/{loan}', 'declineLoan')->name('loan.decline-loan');
            Route::post('request-approve/{loan}', 'requestApproveLoan')->name('loan.request-approval');
            Route::post('get-reloan-deduction', 'getReloanDeduction')->name('loan.get-reloan-deduction');
            Route::get('{id}/print', 'printReceipt')->name('loan.print');
            Route::post('{id}/update-disbursement-reference', 'updateDisbursementReference')->name('loan.update-disbursement-reference');
            Route::get('/report/{id}/preview/soa-term', 'previewSoaTerm')->name('loan.preview-soa-term'); //mockup route for viewing soa
            Route::get('/report/{id}/download-pdf/soa-term', 'downloadSoaTerm')->name('loan.download-pdf-soa-term');
            Route::get('/report/{id}/preview/soa-no-term', 'previewSoaNoTerm')->name('loan.preview-soa-no-term');
            Route::get('/report/{id}/download-pdf/soa-no-term', 'downloadSoaNoTerm')->name('loan.download-pdf-soa-no-term');


        });
    Route::controller(SavingsController::class)
        ->prefix('savings')
        ->group(function () {
            Route::get('{client_id}/transactions/create', 'createTransaction')->name('savings.transactions.create');
            Route::get('{client_id}/transactions', 'transactions')->name('savings.transactions');
            Route::get('{id}/print', 'printReceipt')->name('savings.print');
            Route::post('{id}/change-transaction-code', 'updateTransactionCode')->name('savings.update-transaction-code');
            Route::post('submit-multiple-entries', 'saveMultipleEntries')->name('savings.save-multiple-entries');
        });
    Route::resource('savings', SavingsController::class);
    Route::resource('loan', LoanController::class);
    Route::resource('loan-type', LoanTypeController::class);
    Route::resource('loan-product', LoanProductController::class);
    Route::resource('charge', ChargeController::class);

    Route::controller(ExpenseController::class)
        ->prefix('expenses')
        ->group(function() {
            Route::get('report', 'expenseReport')->name('expenses.report');
            Route::get('report/generate-expense-report', 'generateExpenseReport')->name('expense.generate-expense-report');
            Route::get('report/{slug}', 'viewExpenseReport')->name('expenses.view-report');
            Route::post('create', 'generateExpenseReport')->name('expense.create-report');
            Route::get('edit-modal', 'expenseEditModal')->name('expense.edit-modal');
            Route::post('update', 'updateExpenses')->name('expense.update');
            Route::delete('delete', 'deleteExpense')->name('expense.delete');
            Route::get('view-expense-data', 'viewExpenseData')->name('expense.view-expense-data');
            Route::get('download-expense-details-pdf', 'downloadExpenseDetailsPdf')->name('expense.download-expense-details-pdf');
            Route::get('download-expense-details-images', 'downloadExpenseDetailsImages')->name('expense.download-expense-details-images');
            Route::get('report/download-pdf/{slug}', 'downloadPdf')->name('expense-category.download-pdf');
        });
    Route::resource('expenses',ExpenseController::class);

    Route::controller(ExpenseCategoryController::class)
        ->prefix('expense-category')
        ->group(function(){
            Route::get('/', 'index')->name('expense-category.index');
            Route::post('create', 'create')->name('expense-category.create');
            Route::put('update', 'update')->name('expense-category.update');
        });






    // Others (Group: requires auth)
// Walk-in Index page (web view)
    Route::get('walkin', [WalkinController::class, 'index'])->name('walkin.index');

    // CRUD endpoints (AJAX / fetch)
    Route::post('walkin/store', [WalkinController::class, 'store'])->name('walkin.store');
    Route::get('walkin/show/{id}', [WalkinController::class, 'show'])->name('walkin.show');
    Route::post('walkin/put', [WalkinController::class, 'update'])->name('walkin.update');
    Route::delete('walkin/delete/{id}', [WalkinController::class, 'destroy'])->name('walkin.destroy');

    Route::get('/walkin/{id}/receipt', [WalkinController::class, 'printReceipt'])->name('walkin.receipt');


    // Check Encashments Index
    Route::get('check-encashments', [CheckEncashmentController::class, 'index'])->name('check-encashments.index');

    // CRUD endpoints
    Route::post('check-encashments/store', [CheckEncashmentController::class, 'store'])->name('check-encashments.store');
    Route::get('check-encashments/{id}/get', [CheckEncashmentController::class, 'show'])->name('check-encashments.show');
    Route::put('check-encashments/update', [CheckEncashmentController::class, 'update'])->name('check-encashments.update');
    Route::delete('check-encashments/{id}', [CheckEncashmentController::class, 'destroy'])->name('check-encashments.destroy');

    Route::get('/check-encashments/{id}/receipt', [CheckEncashmentController::class, 'printReceipt'])->name('check-encashments.receipt');



    Route::prefix('service-fees')->group(function () {
        Route::get('/', [ServiceChargeController::class, 'index'])->name('service-fees.index');
        Route::post('/', [ServiceChargeController::class, 'store'])->name('service-fees.store');
        Route::get('/{id}', [ServiceChargeController::class, 'show'])->name('service-fees.show');
        Route::put('/{id}', [ServiceChargeController::class, 'update'])->name('service-fees.update');
        Route::delete('/{id}', [ServiceChargeController::class, 'destroy'])->name('service-fees.destroy');
    });


    Route::prefix('incentives')->group(function () {
        Route::get('/{id}', [IncentiveController::class, 'show'])->name('incentives.show');
        Route::put('/{id}', [IncentiveController::class, 'update'])->name('incentives.update');
        Route::delete('{id}', [IncentiveController::class, 'destroy'])->name('incentives.destroy');
        Route::get('{id}/print', [IncentiveController::class, 'printReceipt'])->name('incentives.print');
    });

    Route::prefix('document-type')->group(function () {
        Route::get('/', [DocumentTypeController::class, 'index'])->name('document-type.index');
        Route::post('store', [DocumentTypeController::class, 'store'])->name('document-type.add');
        Route::put('update',[DocumentTypeController::class, 'update'])->name('document-type.update');
    });

    Route::prefix('vendors')->group(function() {
        route::get('/', [VendorController::class, 'index'])->name('vendors.index');
        route::post('new-vendor', [VendorController::class, 'store'])->name('vendor.store');
        route::post('get-vendor-data', [VendorController::class, 'getVendorData'])->name('vendor.get-vendor-data');
        route::post('get-vendors-list', [VendorController::class, 'getVendorsList'])->name('vendor.get-vendors-list');
        route::put('update-vendor', [VendorController::class, 'update'])->name('vendor.update');
        route::delete('destroy', [VendorController::class, 'destroy'])->name('vendor.destroy');
        Route::post('new-vendor-type', [VendorController::class, 'createVendorType'])->name('vendor.create-vendor-type');
        Route::get('get-vendor-type-data/{id}', [VendorController::class, 'getVendorTypeData'])->name('vendor.get-vendor-type-data');
        Route::put('update-vendor-type', [VendorController::class, 'updateVendorType'])->name('vendor.update-vendor-type');
        Route::delete('delete-vendor-type/{id}', [VendorController::class, 'destroyVendorType'])->name('vendor.delete-vendor-type');
    });


    Route::prefix('bank')->group(function(){
        Route::get('/', [BankController::class, 'index'])->name('bank.index');
        Route::get('get-bank-data/{id}', [BankController::class, 'getBankData'])->name('bank.get-data');
        Route::post('store', [BankController::class, 'store'])->name('bank.store');
        Route::put('put', [BankController::class, 'put'])->name('bank.put');
        Route::delete('delete/{id}', [BankController::class, 'delete'])->name('bank.delete');

    });

    include_once(__DIR__ . '/ajax.php');


});