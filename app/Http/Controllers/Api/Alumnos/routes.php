<?php
// v1

Route::prefix('v1')->group(function () {
    Route::prefix('alumnos')->group(function () {
        Route::get('persona/{id}', 'Api\Alumnos\v1\AlumnosCrud@getByPersonaId');
        Route::get('{ciclo}/{centro}/{curso}/contacto', 'Api\Alumnos\v1\AlumnosContacto@index');
    });

    Route::resource('alumnos', 'Api\Alumnos\v1\AlumnosCrud');
});
