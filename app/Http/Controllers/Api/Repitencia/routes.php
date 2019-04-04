<?php
// API v1
Route::prefix('v1')->group(function () {
    Route::prefix('repitencia')->group(function () {
        Route::get('/', 'Api\Repitencia\v1\RepitenciaCrud@index');
    });
});
