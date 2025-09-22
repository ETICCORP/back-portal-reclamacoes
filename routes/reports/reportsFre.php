<?php

use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Permission\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/getBycode/{id}', [ComplaintController::class, 'getBycode'])->name('reportsFre.php.index');
Route::post('/', [ComplaintController::class, 'store'])->name('reportsFre.php.store');


