<?php
Route::prefix('v1')->group(function () {
    Route::prefix('matriculas')->group(function () {
        Route::prefix('cuantitativa')->group(function () {
            Route::get('por_seccion', 'Api\Matriculas\v1\MatriculasPorSeccion@start');
            Route::get('por_establecimiento', 'Api\Matriculas\v1\MatriculasPorEstablecimiento@start');
            Route::get('por_anio', 'Api\Matriculas\v1\MatriculasPorAnio@start');
            Route::get('por_nivel', 'Api\Matriculas\v1\MatriculasPorNivel@start');
        });
    });
});