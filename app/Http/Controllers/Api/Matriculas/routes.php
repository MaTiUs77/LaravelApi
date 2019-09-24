<?php

Route::prefix('matriculas')->group(function () {

    Route::prefix('cuantitativa')->group(function () {
        Route::get('por_seccion', 'Api\Matriculas\v1\MatriculasPorSeccion@start');
        Route::get('por_anio', 'Api\Matriculas\MatriculasPorAnio@start');
        Route::get('por_nivel', 'Api\Matriculas\MatriculasPorNivel@start');
        Route::get('por_establecimiento', 'Api\Matriculas\MatriculasPorEstablecimiento@start');
    });

    Route::get('recuento/vacantes', 'Api\Matriculas\Matriculas@recuentoVacantes');
    Route::get('recuento/{cicloNombre}', 'Api\Matriculas\Matriculas@recuento');
    Route::get('reset', 'Api\Matriculas\Matriculas@resetMatriculaYPlazas');

});