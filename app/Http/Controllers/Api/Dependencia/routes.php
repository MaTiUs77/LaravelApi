<?php
Route::prefix('dependencia')->group(function () {
    Route::prefix('rrhh')->group(function () {
        Route::get('nominal_alumnos_inscriptos', 'Api\Dependencia\RRHH\NominalAlumnosInscriptos@start');
        Route::get('matriculas_por_seccion', 'Api\Matriculas\v1\MatriculasPorSeccion@start');
    });
});

