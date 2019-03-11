<?php
Route::prefix('saneo')->group(function () {
    Route::get('sorteo/{nivel_servicio}', 'Api\Saneo\SaneoSorteo@start');
});

