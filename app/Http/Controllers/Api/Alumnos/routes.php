<?php
// v1
Route::get('/v1/alumnos/persona/{id}', 'Api\Alumnos\v1\AlumnosCrud@getByPersonaId');
Route::resource('/v1/alumnos', 'Api\Alumnos\v1\AlumnosCrud');