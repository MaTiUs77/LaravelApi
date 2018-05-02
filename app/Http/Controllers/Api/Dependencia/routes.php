<?php
Route::prefix('dependencia')->group(function () {
    Route::prefix('rrhh')->group(function () {
        Route::get('nominal_alumnos_inscriptos', 'Api\Dependencia\RRHH\NominalAlumnosInscriptos@start');
    });

    Route::prefix('estadistica')->group(function () {
        Route::get('matricula_por_seccion', 'Api\Dependencia\Estadistica\MatriculasPorSeccion@start');
    });
});

