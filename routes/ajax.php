<?php

use App\Http\Controllers\AjaxController;
use Illuminate\Support\Facades\Route;

Route::get('/ajax/clients', [AjaxController::class, 'getClients'])->name('ajax.get-clients');
Route::get('/ajax/merchant-slip', [AjaxController::class, 'getAvailableMerchantSlipNo'])->name('ajax.get-merchant-slip');