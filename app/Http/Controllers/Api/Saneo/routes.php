<?php
Route::prefix('saneo')->group(function () {
    Route::get('sorteo/{nivel_servicio}', 'Api\Saneo\SaneoSorteo@start');
});

// V1
Route::prefix('v1')->group(function () {
    Route::prefix('saneo')->group(function () {
        Route::get('repitencia', 'Api\Saneo\SaneoRepitencia@start');
        Route::get('edad', 'Api\Saneo\SaneoEdad@start');
    });
});

