<?php

use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Complaint\TypeComplaintsController;
use App\Http\Controllers\Permission\PermissionController;
use Illuminate\Support\Facades\Route;
Route::get('/', [ComplaintController::class, 'index'])->name('reports.index');

Route::get('/getBycode/{id}', [ComplaintController::class, 'getBycode'])->name('reportsFre.php.index');

Route::get('/type', [TypeComplaintsController::class, 'index']);Route::
post('/type', [TypeComplaintsController::class, 'store']);
Route::get('/type/{id}', [TypeComplaintsController::class, 'show']);
Route::put('/type/{id}', [TypeComplaintsController::class, 'update']);
Route::delete('/type/{id}', [TypeComplaintsController::class, 'destroy']);
Route::post('/', [ComplaintController::class, 'store'])->name('reportsFre.php.store');

