<?php
// v1
Route::get('/v1/alumnos_familiars/alumnos/{familiar_id}','Api\AlumnosFamiliars\v1\AlumnosFamiliarsCrud@getByFamiliar');
Route::resource('/v1/alumnos_familiars', 'Api\AlumnosFamiliars\v1\AlumnosFamiliarsCrud');