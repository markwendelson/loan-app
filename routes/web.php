<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientIDsController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsuranceFeeScheduleController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanProductController;
use App\Http\Controllers\LoanTypeController;
use App\Http\Controllers\NotaryFeeScheduleController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    Route::get('employee-list', [EmployeeController::class, 'index'])->name('employee.index');
    Route::any('employee-registration', [EmployeeController::class, 'register'])->name('employee.register');
    Route::match(['get', 'post'],'employee-update/{id}', [EmployeeController::class, 'update'])->name('employee.update');

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

    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class);
    Route::resource('client', ClientController::class);
    Route::resource('insurance-fee-schedule', InsuranceFeeScheduleController::class);
    Route::resource('notary-fee-schedule', NotaryFeeScheduleController::class);
    Route::prefix('loan')->group(function() {
        Route::get('pending', [LoanController::class, 'pending'])->name('loan.pending');
        Route::get('awaiting-disbursement', [LoanController::class, 'awaitingDisbursement'])->name('loan.awaiting-disbursement');
        Route::get('declined', [LoanController::class, 'declined'])->name('loan.declined');
        Route::get('withdrawn', [LoanController::class, 'withdrawn'])->name('loan.withdrawn');
        Route::get('closed', [LoanController::class, 'closed'])->name('loan.closed');
    });
    Route::resource('loan', LoanController::class);
    Route::resource('loan-type', LoanTypeController::class);
    Route::resource('loan-product', LoanProductController::class);
    Route::resource('charge', ChargeController::class);

});