<?php

use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Proviver\ProviderController;
use Illuminate\Support\Facades\Route;




Route::get('/', [ProviderController::class, 'index'])->name('provider.index');
Route::post('/', [ProviderController::class, 'store'])->name('provider.store');
Route::get('/{id}', [ProviderController::class, 'show'])->name('provider.show');
Route::put('/{id}', [ProviderController::class, 'update'])->name('provider.update');
Route::delete('/{id}', [ProviderController::class, 'destroy'])->name('provider.destroy');
