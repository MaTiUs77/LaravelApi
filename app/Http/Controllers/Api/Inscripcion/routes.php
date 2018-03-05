<?php
Route::prefix('inscripcion')->group(function () {

    Route::prefix('find')->group(function () {
        Route::get('id/{inscripcion_id}', 'Api\Inscripcion\InscripcionFind@byId');
        Route::get('legajo/{legajo_nro}', 'Api\Inscripcion\InscripcionFind@byLegajo');
    });

    Route::prefix('export')->group(function () {
        Route::get('excel', 'Api\Inscripcion\InscripcionExport@excel');
    });

    Route::post('egreso', 'Api\Inscripcion\InscripcionEgreso@start');

    Route::get('lista', 'Api\Inscripcion\Inscripcion@lista');
    Route::get('merge', 'Api\Inscripcion\Inscripcion@mergeLista');

    Route::post('add','Api\Inscripcion\Inscripcion@add');
});

