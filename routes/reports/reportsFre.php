<?php

use App\Http\Controllers\Complaint\Proviver\ComplaintProviderController;
use Illuminate\Support\Facades\Route;

Route::prefix('providers')->group(function () {
    Route::post('forward', [ComplaintProviderController::class, 'forwardComplaint']);
});
