<?php

use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Complaint\TypeComplaintsController;
use App\Http\Controllers\Permission\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComplaintController::class, 'index'])->name('reports.index');

Route::put('/{id}', [ComplaintController::class, 'update'])->name('reports.update');

Route::get('/show/{id}/file', [ComplaintController::class, 'showFile'])->name('reports.showFile');

Route::delete('/{id}', [ComplaintController::class, 'destroy'])->name('reports.destroy');


Route::get('/total', [ComplaintController::class, 'total'])->name('complaints.total');
Route::get('/total-current-week', [ComplaintController::class, 'totalForCurrentWeek'])->name('complaints.totalCurrentWeek');
Route::get('/total-last-week', [ComplaintController::class, 'totalForLastWeek'])->name('complaints.totalLastWeek');
Route::get('/types', [ComplaintController::class, 'getTopTypes'])->name('complaints.types');
Route::get('/count-by-date', [ComplaintController::class, 'countByDate'])->name('complaints.countByDate');
Route::post('/count-by-date', [ComplaintController::class, 'countByDate'])->name('countByDate.index');




