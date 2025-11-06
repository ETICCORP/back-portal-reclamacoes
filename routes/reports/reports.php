<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complaint\ComplaintController;
use App\Http\Controllers\Complaint\ComplaintDeadlineController;
use App\Http\Controllers\Complaint\ComplaintInteraction\ComplaintInteractionController;
use App\Http\Controllers\Complaint\ComplaintOpinionsController;
use App\Http\Controllers\Complaint\ComplaintResponsesController;
use App\Http\Controllers\Complaint\ModelEmail\ModelEmailController;
use App\Http\Controllers\Complaint\Proviver\ComplaintProviderController;
use App\Http\Controllers\Complaint\Proviver\ComplaintProviderResponseController;
use App\Http\Controllers\Complaint\TypeComplaintsController;
use App\Http\Controllers\ComplaintTriages\ComplaintTriagesController;
use App\Http\Controllers\Permission\PermissionController;
use App\Services\Complaint\ComplaintResponsesService;

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

Route::get('/byManth', [ComplaintController::class, 'byManth'])->name('complaints.byManth');

Route::get('/repeatOffenders', [ComplaintController::class, 'repeatOffenders'])->name('complaints.repeatOffenders');


Route::get('/percentageServicedWithinDeadline', action: [ComplaintDeadlineController::class, 'percentageServicedWithinDeadline'])->name('complaints.percentageServicedWithinDeadline');


// Atualização de status
Route::put('/updateStatus/{id}', [ComplaintController::class, 'updateStatus'])
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
/*

    |--------------------------------------------------------------------------
    |Possibilidade de Registo do envio e cópia do conteúdo na base de dados;s. (opinions)
    |--------------------------------------------------------------------------
  */
Route::prefix('responses')->group(function () {
    Route::get('/', [ComplaintResponsesController::class, 'index'])->name('responses.index');
    Route::post('/', [ComplaintResponsesController::class, 'complaintResponse'])->name('responses.complaintResponse');
    Route::get('/{id}', [ComplaintResponsesController::class, 'show'])
        ->whereNumber('id')
        ->name( 'response.show');
    Route::post('/{id}/sendEmailResponse', [ComplaintResponsesController::class, 'sendEmailResponse']);
    Route::post('/', [ComplaintResponsesController::class, 'complaintResponse'])->name('responses.complaintResponse');

    Route::delete('/{id}', [ComplaintResponsesController::class, 'destroy'])->whereNumber('id')->name('responses.destroy');
});


Route::prefix('modelEmail')->group(function () {
    Route::get('/', [ModelEmailController::class, 'index']);
    Route::post('/', [ModelEmailController::class, 'complaintResponse']);
    Route::get('/{id}', [ModelEmailController::class, 'show'])
        ->whereNumber('id')
        ->name('modelEmail.show');

         Route::put('/{id}', [ModelEmailController::class, 'update']);
    Route::post('/{id}/sendEmailResponse', [ModelEmailController::class, 'sendEmailResponse']);
    Route::post('/', [ModelEmailController::class, 'complaintResponse']);
    Route::delete('/{id}', [ModelEmailController::class, 'destroy'])->whereNumber('id');
});



Route::prefix('providers')->group(function () {
    // Encaminhar reclamação
    Route::post('forward', [ComplaintProviderController::class, 'forwardComplaint']);
    Route::get('forward', [ComplaintProviderController::class, 'index']);

    Route::get('response', [ComplaintProviderResponseController::class, 'index']);
    Route::post('response', [ComplaintProviderResponseController::class, 'store']);
});


// Corrigido aqui
Route::get('/count-by-forward-providers', [ComplaintProviderController::class, 'forward'])
    ->name('complaints.forward');

Route::get('/count-by-forward-providers-manth', [ComplaintProviderController::class, 'providersManth'])
    ->name('complaints.providersManth');
