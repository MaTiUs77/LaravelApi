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

    Route::resource('personas', 'Api\Pub\AppFamiliares\v1\Personas');
});

/**
 * SIEP ADMIN
*/

Route::prefix('siep_admin/v1')->group(function () {

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