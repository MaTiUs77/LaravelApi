<?php
// DEPRECAR!
Route::prefix('inscripcion')->group(function () {

    Route::prefix('find')->group(function () {
        Route::get('id/{inscripcion_id}', 'Api\Inscripcion\InscripcionFind@byId');
        Route::get('legajo/{legajo_nro}', 'Api\Inscripcion\InscripcionFind@byLegajo');
        
        Route::get('persona/id/{persona_id}', 'Api\Inscripcion\InscripcionFind@byPersona');
        Route::get('persona', 'Api\Inscripcion\InscripcionFind@startFind');

        // LEGACY Antes de deprecar esta ruta, es necesario quitarla de siep y de la documentacion
        Route::get('persona/{persona_id}', 'Api\Inscripcion\InscripcionFind@byPersona');
        Route::get('persona', 'Api\Inscripcion\InscripcionFind@byPersonaFullname');
    });

    Route::prefix('export')->group(function () {
        Route::get('excel', 'Api\Inscripcion\InscripcionExport@excel');
    });

    Route::get('lista', 'Api\Inscripcion\Inscripcion@lista');

    Route::post('/egreso', 'Api\Inscripcion\InscripcionEgreso@start');
    Route::post('/reubicacion', 'Api\Inscripcion\InscripcionReubicacion@start');
});