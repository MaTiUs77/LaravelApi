<?php
// API v2
Route::prefix('v2')->group(function () {
    Route::prefix('inscripcion')->group(function () {
        Route::get('id/{inscripcion_id}', 'Api\Inscripcion\v2\InscripcionFind@byId');
    });
});

