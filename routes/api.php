<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalkinController;

// Walk-in API routes
Route::get('/walkin', [WalkinController::class, 'getAll']);
Route::post('/walkin', [WalkinController::class, 'store']);
Route::put('/walkin/{walkin}', [WalkinController::class, 'update']);
Route::delete('/walkin/{walkin}', [WalkinController::class, 'destroy']);
