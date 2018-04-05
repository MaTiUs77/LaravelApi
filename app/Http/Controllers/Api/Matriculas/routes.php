<?php

Route::prefix('matriculas')->group(function () {
    Route::get('cuantitativa/por_nivel', 'Api\Matriculas\Matriculas@cuantitativaPorNivel');
    Route::get('cuantitativa', 'Api\Matriculas\Matriculas@cuantitativa');

    Route::get('recuento/{cicloNombre}', 'Api\Matriculas\Matriculas@recuento')
        ->middleware(['jwt.auth']);

    Route::get('reset', 'Api\Matriculas\Matriculas@resetMatriculaYPlazas')
        ->middleware(['jwt.auth']);
});



