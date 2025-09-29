<?php

use App\Http\Controllers\Complaint\ComplaintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


Route::get('/reports/show/{id}/file', [ComplaintController::class, 'showFile'])->name('reports.showFile');