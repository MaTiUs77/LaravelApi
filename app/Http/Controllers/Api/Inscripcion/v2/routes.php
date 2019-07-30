<?php
// API v1
Route::prefix('v2')->group(function () {
    Route::prefix('inscripcion')->namespace('Api\Inscripcion\v2')->group(function () {
        Route::get('id/{inscripcion_id}', 'InscripcionFind@byId');
    });
});