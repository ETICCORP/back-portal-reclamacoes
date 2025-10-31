<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Complaint\ComplaintDeadlineController;
use App\Http\Controllers\Complaint\ComplaintInteraction\ComplaintInteractionController;
use App\Http\Controllers\Complaint\ComplaintOpinionsController;
use App\Http\Controllers\Complaint\TypeComplaintsController;
use App\Http\Controllers\ComplaintTriages\ComplaintTriagesController;
use App\Http\Controllers\Permission\PermissionController;

/*
|--------------------------------------------------------------------------
| Complaint Routes (Agrupadas em /api/reports)
|--------------------------------------------------------------------------
|
| As rotas são organizadas com prefixos distintos e restrições
| para evitar conflitos entre / e /{id}.
|
*/



    /*
    |--------------------------------------------------------------------------
    | Reclamações (Complaints)
    |--------------------------------------------------------------------------
    */
   
        // CRUD principal
        Route::get('/', [ComplaintController::class, 'index'])->name('complaints.index');
        Route::post('/', [ComplaintController::class, 'store'])->name('complaints.store');
        Route::get('/{id}', [ComplaintController::class, 'show'])
            ->whereNumber('id')
            ->name('complaints.show');
        Route::put('/{id}', [ComplaintController::class, 'update'])->whereNumber('id')->name('complaints.update');
        Route::delete('/{id}', [ComplaintController::class, 'destroy'])->whereNumber('id')->name('complaints.destroy');

        // Totais e estatísticas
        Route::get('/total', [ComplaintController::class, 'total'])->name('complaints.total');
        Route::get('/total-current-week', [ComplaintController::class, 'totalForCurrentWeek'])->name('complaints.totalCurrentWeek');
        Route::get('/total-last-week', [ComplaintController::class, 'totalForLastWeek'])->name('complaints.totalLastWeek');
        Route::get('/types', [ComplaintController::class, 'getTopTypes'])->name('complaints.types');
        Route::match(['get', 'post'], '/count-by-date', [ComplaintController::class, 'countByDate'])->name('complaints.countByDate');
        Route::get('/time-of-response', [ComplaintController::class, 'timeResponse'])->name('complaints.timeResponse');

        // Atualização de status
        Route::put('/update-status/{id}', [ComplaintController::class, 'updateStatus'])
            ->whereNumber('id')
            ->name('complaints.updateStatus');
    


    /*
    |--------------------------------------------------------------------------
    | Triagem e Classificação (Complaint Triages)
    |--------------------------------------------------------------------------
    */
    Route::prefix('triages')->group(function () {
        Route::get('/', [ComplaintTriagesController::class, 'index'])->name('complaintTriages.index');
        Route::post('/', [ComplaintTriagesController::class, 'store'])->name('complaintTriages.store');
        Route::get('/{id}', [ComplaintTriagesController::class, 'show'])
            ->whereNumber('id')
            ->name('complaintTriages.show');
        Route::put('/{id}', [ComplaintTriagesController::class, 'update'])->whereNumber('id')->name('complaintTriages.update');
        Route::delete('/{id}', [ComplaintTriagesController::class, 'destroy'])->whereNumber('id')->name('complaintTriages.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Gestão de Prazos Legais (Complaint Deadlines)
    |--------------------------------------------------------------------------
    */
    Route::prefix('deadlines')->group(function () {
        Route::get('/', [ComplaintDeadlineController::class, 'index'])->name('complaintDeadlines.index');
        Route::post('/', [ComplaintDeadlineController::class, 'store'])->name('complaintDeadlines.store');
        Route::get('/{id}', [ComplaintDeadlineController::class, 'show'])
            ->whereNumber('id')
            ->name('complaintDeadlines.show');
        Route::put('/{id}', [ComplaintDeadlineController::class, 'update'])->whereNumber('id')->name('complaintDeadlines.update');
        Route::delete('/{id}', [ComplaintDeadlineController::class, 'destroy'])->whereNumber('id')->name('complaintDeadlines.destroy');
    });



     /*
    |--------------------------------------------------------------------------
    | Gestão de Prazos Legais (Complaint Deadlines)
    |--------------------------------------------------------------------------
    */
    Route::prefix('deadlines')->group(function () {
        Route::get('/', [ComplaintDeadlineController::class, 'index'])->name('complaintDeadlines.index');
        Route::post('/', [ComplaintDeadlineController::class, 'store'])->name('complaintDeadlines.store');
        Route::get('/{id}', [ComplaintDeadlineController::class, 'show'])
            ->whereNumber('id')
            ->name('complaintDeadlines.show');
        Route::put('/{id}', [ComplaintDeadlineController::class, 'update'])->whereNumber('id')->name('complaintDeadlines.update');
        Route::delete('/{id}', [ComplaintDeadlineController::class, 'destroy'])->whereNumber('id')->name('complaintDeadlines.destroy');
    });



     /*
    |--------------------------------------------------------------------------
    | Registo de contactos realizados com o reclamante(contacts)
    |--------------------------------------------------------------------------
    */
    Route::prefix('interaction')->group(function () {
        Route::get('/', [ComplaintInteractionController::class, 'index'])->name('interaction.index');
        Route::post('/', [ComplaintInteractionController::class, 'store'])->name('interaction.store');
        Route::get('/{id}', [ComplaintInteractionController::class, 'show'])
            ->whereNumber('id')
            ->name('interaction.show');
        Route::put('/{id}', [ComplaintInteractionController::class, 'update'])->whereNumber('id')->name('interaction.update');
        Route::delete('/{id}', [ComplaintInteractionController::class, 'destroy'])->whereNumber('id')->name('interaction.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    |Possibilidade de envolver outras áreas com registo de pareceres. (opinions)
    |--------------------------------------------------------------------------
    */
    Route::prefix('opinions')->group(function () {
        Route::get('/', [ComplaintOpinionsController::class, 'index'])->name('opinions.index');
        Route::post('/', [ComplaintOpinionsController::class, 'store'])->name('opinions.store');
        Route::get('/{id}', [ComplaintOpinionsController::class, 'show'])
            ->whereNumber('id')
            ->name('opinions.show');
        Route::put('/{id}', [ComplaintOpinionsController::class, 'update'])->whereNumber('id')->name('opinions.update');
        Route::delete('/{id}', [ComplaintOpinionsController::class, 'destroy'])->whereNumber('id')->name('opinions.destroy');
    });

    