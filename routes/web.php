<?php

use App\Http\Controllers\Complaint\ComplaintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


