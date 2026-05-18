<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

Route::get('/me', [UserController::class, 'me'])
    ->name('user.me');
Route::put('/changePassword', [UserController::class, 'changePassword'])
    ->name('user.changePassword');

Route::put('/changePassword/{id}', [UserController::class, 'changePasswordUser'])
    ->middleware(['permissao:usuario-change-password']);

Route::get('', [UserController::class, 'index'])
    ->name('user.index')
    ->middleware(['permissao:usuario-show']);

Route::post('', [UserController::class, 'store'])
    ->name('user.store')
    ->middleware(['permissao:usuario-create']);

Route::get('{id}', [UserController::class, 'show'])
    ->name('user.show')
    ->middleware(['permissao:usuario-show']);

Route::put('{id}', [UserController::class, 'update'])
    ->name('user.update')
    ->middleware(['permissao:usuario-edit']);

Route::put('/enabled/{id}', [UserController::class, 'enabled'])
    ->name('user.enabled');

Route::delete('{id}', [UserController::class, 'destroy'])
    ->name('user.destroy')
    ->middleware(['permissao:usuario-delete']);

Route::post('/logout', [UserController::class, 'logout']);