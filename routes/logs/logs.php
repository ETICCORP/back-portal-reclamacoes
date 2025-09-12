<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Log\LogController;


Route::get('/', [LogController::class, 'index'])
    ->name('logs.index');
