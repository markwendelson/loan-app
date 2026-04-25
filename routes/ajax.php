<?php

use App\Http\Controllers\AjaxController;
use Illuminate\Support\Facades\Route;

Route::get('/ajax/clients', [AjaxController::class, 'getClients'])->name('ajax.get-clients');
Route::get('/ajax/merchant-slip', [AjaxController::class, 'getAvailableMerchantSlipNo'])->name('ajax.get-merchant-slip');

Route::get('/ajax/client/{client}/incentives', [AjaxController::class, 'clientIncentives'])->name('client.incentives');
Route::post('/ajax/client/{client}/incentives', [AjaxController::class, 'addClientIncentives'])->name('client.add-incentives');

Route::get('/ajax/document-types', [AjaxController::class, 'getDocumentTypes'])->name('ajax.get-document-types');

Route::post('/ajax/attachments/upload', [AjaxController::class, 'uploadAttachment'])->name('ajax.upload-attachment');

Route::delete('/ajax/co-maker', [AjaxController::class, 'deleteCoMaker'])->name('ajax.delete-co-maker');
Route::post('/ajax/co-maker', [AjaxController::class, 'registerCoMaker'])->name('ajax.register-co-maker');