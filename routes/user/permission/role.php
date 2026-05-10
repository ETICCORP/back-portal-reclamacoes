<?php

use App\Http\Controllers\Permission\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('', [RoleController::class, 'index'])
    ->name('role.index')
    ->middleware(['permissao:perfil-show']);

Route::post('', [RoleController::class, 'store'])
    ->name('role.store')
    ->middleware(['permissao:perfil-create']);

Route::put('{role}', [RoleController::class, 'update'])
    ->name('role.update')
    ->middleware(['permissao:perfil-edit']);

Route::delete('{role}', [RoleController::class, 'destroy'])
    ->name('role.destroy')
    ->middleware(['permissao:perfil-delete']);

Route::get('{role}', [RoleController::class, 'show'])
    ->name('role.show')
    ->middleware(['permissao:perfil-show']);
