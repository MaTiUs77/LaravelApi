<?php
// API v1
Route::prefix('v1')->group(function () {
    Route::prefix('inscripcion')->namespace('Api\Inscripcion\v1')->group(function () {
        Route::get('id/{inscripcion_id}', 'InscripcionFind@byId');
        Route::get('find', 'InscripcionFind@startFind');

        Route::get('lista', 'InscripcionList@lista');
        Route::get('lista/excel', 'InscripcionExport@excel');

        Route::get('con_hermano/{ciclo}/{centro_id?}/{curso_id?}', 'InscripcionConHermano@index');

        Route::get('{ciclo}', 'InscripcionRouteFilter@index');
        Route::get('{ciclo}/{centro_id}', 'InscripcionRouteFilter@index');
        Route::get('{ciclo}/{centro_id}/{curso_id}', 'InscripcionRouteFilter@index');
    });
});