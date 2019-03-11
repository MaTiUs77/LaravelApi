<?php
// Deprecada
Route::resource('/personas', 'Api\Personas\v1\PersonasCrud');

Route::prefix('v1')->group(function () {
    Route::get('personas/{persona_id}/ficha', 'Api\Personas\v1\Ficha@index');
    Route::resource('personas', 'Api\Personas\v1\PersonasCrud');
});
