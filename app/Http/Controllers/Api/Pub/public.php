<?php
/*
api/v1/centros
api/v1/barrios
api/v1/personas
api/v1/familiar
api/v1/familiar/persona
api/v1/alumnos
api/v1/alumnos_familiars
 */
Route::prefix('app_familiares/v1')->group(function () {
    Route::resource('centros', 'Api\Centros\v1\CentrosCrud');
    Route::resource('barrios', 'Api\Barrios\v1\BarriosCrud');
    Route::resource('ciudades', 'Api\Ciudades\v1\CiudadesCrud');

    Route::resource('personas', 'Api\Pub\AppFamiliares\v1\PersonasPublicCrud');
    Route::get('familiares/persona/{id}', 'Api\Pub\AppFamiliares\v1\FamiliarPublicCrud@getByPersonaId');
    Route::resource('familiares', 'Api\Pub\AppFamiliares\v1\FamiliarPublicCrud');
    Route::resource('alumnos', 'Api\Pub\AppFamiliares\v1\AlumnoPublicCrud');
    Route::get('alumnos_familiars/alumnos/{id}', 'Api\Pub\AppFamiliares\v1\AlumnosFamiliarsPublicCrud@getByFamiliar');
    Route::resource('alumnos_familiars', 'Api\Pub\AppFamiliares\v1\AlumnosFamiliarsPublicCrud');

    Route::resource('contacto','Api\Pub\AppFamiliares\v1\ContactoPublicCrud');

    Route::prefix('forms')->group(function () {
        Route::get('sectores', 'Api\Forms\Forms@sectores');
        Route::get('niveles', 'Api\Forms\Forms@niveles');
        Route::get('ciclos', 'Api\Forms\Forms@ciclos');
        Route::get('años', 'Api\Forms\Forms@años');
        Route::get('estado_inscripcion', 'Api\Forms\Forms@estado_inscripcion');
        Route::get('turnos', 'Api\Forms\Forms@turnos');
        Route::get('divisiones', 'Api\Forms\Forms@divisiones');
    });
});

/**
 * SIEP ADMIN
*/

Route::prefix('siep_admin/v1')->group(function () {
    Route::resource('centros', 'Api\Centros\v1\CentrosCrud');
    Route::resource('barrios', 'Api\Barrios\v1\BarriosCrud');

    Route::prefix('forms')->group(function () {
        Route::get('ciudades', 'Api\Forms\Forms@ciudades');
        Route::get('centros', 'Api\Forms\Forms@centros');
        Route::get('sectores', 'Api\Forms\Forms@sectores');
        Route::get('niveles', 'Api\Forms\Forms@niveles');
        Route::get('ciclos', 'Api\Forms\Forms@ciclos');
        Route::get('años', 'Api\Forms\Forms@años');
        Route::get('estado_inscripcion', 'Api\Forms\Forms@estado_inscripcion');
        Route::get('turnos', 'Api\Forms\Forms@turnos');
        Route::get('divisiones', 'Api\Forms\Forms@divisiones');
    });

    // Dependencias
    // Route::middleware('jwt')->group(function () {
        Route::prefix('dependencia')->group(function () {
            Route::prefix('rrhh')->group(function () {
                Route::get('nominal_alumnos_inscriptos', 'Api\Dependencia\RRHH\NominalAlumnosInscriptos@start');
            });
        });
    
        // Matriculas
        Route::prefix('matriculas')->group(function () {
            Route::prefix('v1')->group(function () {
                Route::get('matriculas_por_seccion', 'Api\Matriculas\v1\MatriculasPorSeccion@start');
            });
        });
    // });

});

/**
 * INSCRIPCIONES
 */
Route::prefix('inscripcion')->group(function () {
    Route::get('lista', 'Api\Inscripcion\Inscripcion@lista');
});