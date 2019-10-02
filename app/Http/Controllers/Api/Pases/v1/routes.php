<?php
// API v1
Route::prefix('v1')->group(function () {
    Route::prefix('pases')->group(function () {
        Route::resource('/', 'Api\Pases\v1\PasesCrud');
    });
});




