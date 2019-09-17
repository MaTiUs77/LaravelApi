<?php
// v1
Route::prefix('v1')->group(function () {
    Route::prefix('saneo')->group(function () {
        Route::get('repitencia', 'Api\Saneo\v1\SaneoRepitencia@start');
        Route::get('edad', 'Api\Saneo\v1\SaneoEdad@start');
        Route::get('sorteo/{ciclo}/{nivel_servicio}', 'Api\Saneo\v1\SaneoSorteo@start');
    });
});

