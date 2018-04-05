<?php

Route::prefix('matriculas')->group(function () {
    Route::get('cuantitativa/por_anio', 'Api\Matriculas\MatriculasPorAnio@start');
    Route::get('cuantitativa/por_nivel', 'Api\Matriculas\MatriculasPorNivel@start');
    Route::get('cuantitativa/por_establecimiento', 'Api\Matriculas\MatriculasPorEstablecimiento@start');
    Route::get('cuantitativa', 'Api\Matriculas\Matriculas@cuantitativa');

    Route::get('recuento/{cicloNombre}', 'Api\Matriculas\Matriculas@recuento')
        ->middleware(['jwt.auth']);

    Route::get('reset', 'Api\Matriculas\Matriculas@resetMatriculaYPlazas')
        ->middleware(['jwt.auth']);
});



