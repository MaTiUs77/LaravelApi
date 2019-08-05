<?php
Route::prefix('promocion')->group(function () {
    Route::post('/', 'Api\Promocion\Promocion@start');
});