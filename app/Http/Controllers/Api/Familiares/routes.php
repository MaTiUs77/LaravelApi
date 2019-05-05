<?php

Route::prefix('v1')->group(function () {
    Route::get('familiar/persona/{id}', 'Api\Familiares\v1\FamiliarCrud@getByPersonaId');
    Route::resource('familiar', 'Api\Familiares\v1\FamiliarCrud');
});
