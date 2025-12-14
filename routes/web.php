<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientIDsController;
use App\Http\Controllers\DepartmentController;
// use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsuranceFeeScheduleController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanProductController;
use App\Http\Controllers\LoanTypeController;
use App\Http\Controllers\NotaryFeeScheduleController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CheckEncashmentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
// ----- Others
use App\Http\Controllers\WalkinController;
use App\Http\Controllers\CheckController;

Route::get('/', function () {
    return redirect('/dashboard');
});


Route::get('/logout', function() {
    Auth::logout();

    return redirect('/login');
})->name('logout');

Auth::routes();

Route::middleware('auth')->group(function(){

    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');

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
    Route::post('branches', [BranchController::class, 'store'])->name('branch.store');

    //-----Department
    Route::get('departments', [DepartmentController::class, 'index'])->name('department.index');
    Route::post('department/get-department', [DepartmentController::class, 'getDepartmentInfo']);
    Route::post('departments/submit-form', [DepartmentController::class, 'submitDepartment']);

    //-----JobTitle
    Route::get('job-titles', [JobController::class, 'index'])->name('jobTitle.index');
    Route::post('job-titles/get-jobtitle', [JobController::class, 'getJobInfo']);
    Route::post('job-titles/submit-form', [JobController::class, 'submitJob']);

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
        ->group(function() {
            Route::get('{client}/get-balance', 'getBalance')->name('client.get-balance');
            Route::get('{client}/{loan_no}/ledger', 'ledgerLoan')->name('client.ledger.loan');
            Route::get('{client}/loan-history', 'loanHistory')->name('client.loan-history');
            Route::get('{client}/ledger', 'ledgerPrincipal')->name('client.ledger.principal');
            Route::get('{client}/ledger/special', 'ledgerSpecial')->name('client.ledger.special');
            Route::get('{client}/loan-history-details', 'loanHistoryDetails')->name('client.loan-history-details');
            Route::get('{client}/partial-ledger', 'partialLedger')->name('client.partial-ledger');
    });

    Route::post('user/{user}/permission',[UserController::class,'updatePermission'])->name('user.update-permission');

    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class);
    Route::resource('client', ClientController::class);
    Route::resource('insurance-fee-schedule', InsuranceFeeScheduleController::class);
    Route::resource('notary-fee-schedule', NotaryFeeScheduleController::class);
    Route::controller(LoanController::class)
        ->prefix('loan')
        ->group(function() {
            Route::get('pending', 'pending')->name('loan.pending');
            Route::get('awaiting-disbursement', 'awaitingDisbursement')->name('loan.awaiting-disbursement');
            Route::get('declined', 'declined')->name('loan.declined');
            Route::get('withdrawn', 'withdrawn')->name('loan.withdrawn');
            Route::get('closed', 'closed')->name('loan.closed');
            Route::post('disburse/{loan}', 'disburse')->name('loan.disburse');
            Route::post('withdrawn/{loan}','withdrawnLoan')->name('loan.withdrawn-loan');;
            Route::post('approve/{loan}','approveLoan')->name('loan.approve-loan');;
            Route::post('decline/{loan}','declineLoan')->name('loan.decline-loan');;
            Route::post('request-approve/{loan}','requestApproveLoan')->name('loan.request-approval');;
            Route::post('get-reloan-deduction','getReloanDeduction')->name('loan.get-reloan-deduction');;
            Route::get('{id}/print', 'printReceipt')->name('loan.print');
    });
    Route::controller(SavingsController::class)
        ->prefix('savings')
        ->group(function() {
            Route::get('{client_id}/transactions', 'transactions')->name('savings.transactions');
            Route::get('{id}/print', 'printReceipt')->name('savings.print');
    });
    Route::resource('savings', SavingsController::class);
    Route::resource('loan', LoanController::class);
    Route::resource('loan-type', LoanTypeController::class);
    Route::resource('loan-product', LoanProductController::class);
    Route::resource('charge', ChargeController::class);



// Others (Group: requires auth)
// Walk-in Index page (web view)
Route::get('walkin', [WalkinController::class, 'index'])->name('walkin.index');

// CRUD endpoints (AJAX / fetch)
Route::post('walkin', [WalkinController::class, 'store'])->name('walkin.store');
Route::get('walkin/{id}', [WalkinController::class, 'show'])->name('walkin.show');
Route::put('walkin/{id}', [WalkinController::class, 'update'])->name('walkin.update');
Route::delete('walkin/{id}', [WalkinController::class, 'destroy'])->name('walkin.destroy');

Route::get('/walkin/{id}/receipt', [WalkinController::class, 'printReceipt'])->name('walkin.receipt');


// Check Encashments Index
Route::get('check-encashments', [CheckEncashmentController::class, 'index'])->name('check-encashments.index');

// CRUD endpoints
Route::post('check-encashments', [CheckEncashmentController::class, 'store'])->name('check-encashments.store');
Route::get('check-encashments/{id}', [CheckEncashmentController::class, 'show'])->name('check-encashments.show');
Route::put('check-encashments/{id}', [CheckEncashmentController::class, 'update'])->name('check-encashments.update');
Route::delete('check-encashments/{id}', [CheckEncashmentController::class, 'destroy'])->name('check-encashments.destroy');

Route::get('/check-encashments/{id}/receipt', [CheckEncashmentController::class, 'printReceipt'])->name('check-encashments.receipt');



include_once(__DIR__ . '/ajax.php');

});