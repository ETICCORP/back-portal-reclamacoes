<?php

use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Complaint\Proviver\ComplaintProviderResponseController;
use App\Http\Controllers\Permission\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/total', [ComplaintController::class, 'total'])->name('complaints.total');
Route::get('/total-current-week', [ComplaintController::class, 'totalForCurrentWeek'])->name('complaints.totalCurrentWeek');
Route::get('/total-last-week', [ComplaintController::class, 'totalForLastWeek'])->name('complaints.totalLastWeek');
Route::get('/types', [ComplaintController::class, 'getTopTypes'])->name('complaints.types');
Route::get('/count-by-date', [ComplaintController::class, 'countByDate'])->name('complaints.countByDate');

// Corrigido aqui
Route::get('/count-by-forward-providers', [ComplaintProviderResponseController::class, 'forward'])
    ->name('complaints.forward');

Route::get('/count-by-forward-providers-manth', [ComplaintProviderResponseController::class, 'providersManth'])
    ->name('complaints.providersManth');
