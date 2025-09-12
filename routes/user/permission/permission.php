<?php

use App\Http\Controllers\Permission\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PermissionController::class, 'index'])->name('permission.index');
Route::post('/', [PermissionController::class, 'store'])->name('permission.store');
Route::get('/{id}', [PermissionController::class, 'show'])->name('permission.show');
Route::put('/{id}', [PermissionController::class, 'update'])->name('permission.update');
Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('permission.destroy');
