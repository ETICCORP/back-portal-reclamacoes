<?php

use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Permission\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CommentController::class, 'index'])->name('comment.index');
Route::post('/', [CommentController::class, 'store'])->name('comment.store');
Route::get('/{id}', [CommentController::class, 'show'])->name('comment.show');
Route::put('/{id}', [CommentController::class, 'update'])->name('comment.update');
Route::delete('/{id}', [CommentController::class, 'destroy'])->name('comment.destroy');
