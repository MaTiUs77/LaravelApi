<?php
// API v1
Route::prefix('v1')->group(function () {
    Route::prefix('stats')->group(function () {
        Route::get('/', 'Api\Stats\v1\Stats@index');
    });
});
