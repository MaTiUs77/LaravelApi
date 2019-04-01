<?php

Route::prefix('v1')->group(function () {
    Route::resource('familiar', 'Api\Familiares\v1\FamiliarCrud');
});
