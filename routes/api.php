<?php

use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Complaint\ModelEmail\ModelEmailController;
use App\Http\Controllers\Complaint\TypeComplaintsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

Route::prefix(prefix: 'reports')->group(base_path('routes/reports/reportsFre.php'));
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('permission')->group(base_path('routes/user/permission/permission.php'));
    Route::prefix('role')->group(base_path('routes/user/permission/role.php'));
    Route::prefix('user')->group(base_path('routes/user/user.php'));
    Route::prefix('logs')->group(base_path('routes/logs/logs.php'));
    Route::prefix('alert')->group(base_path('routes/alert/alert.php'));
    //Route::prefix('statistics')->group(base_path('routes/statistics/statistics.php'));
    Route::prefix('reports')->group(base_path('routes/reports/reports.php'));
    Route::prefix('comment')->group(base_path('routes/comment/comment.php'));
     Route::prefix('provider')->group(base_path('routes/provider/provider.php'));
});
 
Route::post('/auth/login', [UserController::class, 'login']);
Route::prefix('auth')->middleware('guest')->group(base_path('routes/user/auth.php'));
Route::post('auth/2fa', [UserController::class, 'verify2fa']);


Route::middleware('web')->get('/reports/show/{id}/file', [ComplaintController::class, 'showFile'])
    ->name('reports.showFile');

    Route::middleware('web')->get('/reports/show/{id}/minuta', [ModelEmailController::class, 'showFile'])
   ;
    //routes publics

Route::prefix('reports')->group(function () {
  Route::post('/', [ComplaintController::class, 'store'])->name('reportsFre.php.store');
    Route::get('/getBycode/{id}', [ComplaintController::class, 'getBycode']);
    Route::get('/type', [TypeComplaintsController::class, 'index']);
    Route::post('/type', [TypeComplaintsController::class, 'store']);
    Route::get('/type/{id}', [TypeComplaintsController::class, 'show']);
    Route::put('/type/{id}', [TypeComplaintsController::class, 'update']);
    Route::delete('/type/{id}', [TypeComplaintsController::class, 'destroy']);
  
});
