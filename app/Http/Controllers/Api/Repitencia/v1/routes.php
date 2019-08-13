<?php
// API v1
Route::prefix('v1')->group(function () {
    Route::prefix('repitencia')->group(function () {
        Route::get('/', 'Api\Repitencia\v1\RepitenciaCrud@index');
        Route::post('/', 'Api\Repitencia\v1\RepitenciaCrud@store');

        Route::get('{ciclo}', 'Api\Repitencia\v1\RepitenciaRouteFilter@index');
        Route::get('{ciclo}/{centro_id}', 'Api\Repitencia\v1\RepitenciaRouteFilter@index');
        Route::get('{ciclo}/{centro_id}/{curso_id}', 'Api\Repitencia\v1\RepitenciaRouteFilter@index');
    });
});
