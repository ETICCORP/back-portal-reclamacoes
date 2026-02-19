<?php

use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Complaint\Proviver\ComplaintProviderController;
use App\Http\Controllers\Complaint\TypeComplaintsController;
use Illuminate\Support\Facades\Route;



Route::prefix('providers')->group(function () {
 Route::post('forward', [ComplaintProviderController::class, 'forwardComplaint']);
   
});
