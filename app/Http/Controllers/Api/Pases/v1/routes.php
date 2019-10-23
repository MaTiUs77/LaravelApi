<?php
// API v1
Route::prefix('v1')->group(function () {
    Route::resource('/pases', 'Api\Pases\v1\PasesCrud');
});




