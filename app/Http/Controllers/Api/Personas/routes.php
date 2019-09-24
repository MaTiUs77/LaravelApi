<?php
// Deprecada
Route::resource('/personas', 'Api\Personas\v1\PersonasCrud');

Route::prefix('v1')->group(function () {
    Route::get('personas/{persona_id}/ficha', 'Api\Personas\v1\Ficha@index');
    Route::get('personas/{persona}/trayectoria', 'Api\Personas\v1\PersonaTrayectoria@index');
    Route::get('personas/{persona}/ultimainscripcion', 'Api\Personas\v1\PersonaUltimaInscripcion@index');
    Route::resource('personas', 'Api\Personas\v1\PersonasCrud');
});
