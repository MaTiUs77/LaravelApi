<?php
// Deprecada
Route::resource('/personas', 'Api\Personas\v1\PersonasCrud');

// v1
Route::get('v1/personas/{persona_id}/ficha', 'Api\Personas\v1\Ficha@index');

Route::resource('/v1/personas', 'Api\Personas\v1\PersonasCrud');

