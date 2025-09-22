<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('permission')->group(base_path('routes/user/permission/permission.php'));
    Route::prefix('role')->group(base_path('routes/user/permission/role.php'));
    Route::prefix('user')->group(base_path('routes/user/user.php'));
    Route::prefix('logs')->group(base_path('routes/logs/logs.php'));
    //Route::prefix('statistics')->group(base_path('routes/statistics/statistics.php'));
    Route::prefix('reports')->group(base_path('routes/reports/reports.php'));
    Route::prefix('comment')->group(base_path('routes/comment/comment.php'));
});

Route::post('/auth/login', [UserController::class, 'login']);
Route::prefix('auth')->middleware('guest')->group(base_path('routes/user/auth.php'));
Route::post('auth/2fa', [UserController::class, 'verify2fa']);
Route::prefix('reports')->group(base_path('routes/reports/reportsFre.php'));