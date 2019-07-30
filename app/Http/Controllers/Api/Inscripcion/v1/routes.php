<?php
// API v1
Route::prefix('v1')->group(function () {
    Route::prefix('inscripcion')->group(function () {
        Route::get('id/{inscripcion_id}', 'Api\Inscripcion\v1\InscripcionFind@byId');
        Route::get('find', 'Api\Inscripcion\v1\InscripcionFind@startFind');

        Route::get('lista', 'Api\Inscripcion\v1\InscripcionList@lista');
        Route::get('lista/excel', 'Api\Inscripcion\v1\InscripcionExport@excel');

        Route::get('{ciclo}', 'Api\Inscripcion\v1\InscripcionRouteFilter@index');
        Route::get('{ciclo}/{centro_id}', 'Api\Inscripcion\v1\InscripcionRouteFilter@index');
        Route::get('{ciclo}/{centro_id}/{curso_id}', 'Api\Inscripcion\v1\InscripcionRouteFilter@index');
    });
});