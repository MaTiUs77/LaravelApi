<?php
// Deprecada
Route::resource('/personas', 'Api\Personas\v1\PersonasCrud');

// v1
Route::resource('/v1/personas', 'Api\Personas\v1\PersonasCrud');