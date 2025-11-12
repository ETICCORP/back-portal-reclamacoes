<?php

use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Proviver\grupProveder\grupProvederController;
use App\Http\Controllers\Proviver\ProviderController;
use Illuminate\Support\Facades\Route;


Route::prefix('users')->group(function () {
   
Route::get('/', [grupProvederController::class, 'index'])->name('users.provider.index');
Route::post('/', [grupProvederController::class, 'store'])->name('users.provider.store');
Route::get('/{id}', [grupProvederController::class, 'show'])->name('users.provider.show');
Route::put('/{id}', [grupProvederController::class, 'update'])->name('users.provider.update');
Route::delete('/{id}', [grupProvederController::class, 'destroy'])->name('users.provider.destroy');

});

Route::get('/', [ProviderController::class, 'index'])->name('provider.index');
Route::post('/', [ProviderController::class, 'store'])->name('provider.store');
Route::get('/{id}', [ProviderController::class, 'show'])->name('provider.show');
Route::put('/{id}', [ProviderController::class, 'update'])->name('provider.update');
Route::delete('/{id}', [ProviderController::class, 'destroy'])->name('provider.destroy');


