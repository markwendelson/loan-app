<?php

use App\Http\Controllers\Api\HealthCheckController;

Route::get('/health-check', HealthCheckController::class);

// use App\Http\Controllers\WalkinController;

// Route::get('/walkin', [WalkinController::class, 'index'])->name('api.walkin.index');
// Route::get('/walkin/{id}', [WalkinController::class, 'show'])->name('api.walkin.show');
// Route::post('/walkin', [WalkinController::class, 'store'])->name('api.walkin.store');
// Route::put('/walkin/{id}', [WalkinController::class, 'update'])->name('api.walkin.update');
// Route::delete('/walkin/{id}', [WalkinController::class, 'destroy'])->name('api.walkin.destroy');