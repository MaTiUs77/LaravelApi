<?php
// API v1
Route::prefix('v1')->group(function () {
    Route::prefix('promocion')->group(function () {

        Route::get('/', 'Api\Promocion\v1\PromocionCrud@index');
        Route::post('/', 'Api\Promocion\v1\PromocionCrud@store');

        Route::get('{ciclo}', 'Api\Promocion\v1\PromocionRouteFilter@index');
        Route::get('{ciclo}/{centro_id}', 'Api\Promocion\v1\PromocionRouteFilter@index');
        Route::get('{ciclo}/{centro_id}/{curso_id}', 'Api\Promocion\v1\PromocionRouteFilter@index');
    });
});
