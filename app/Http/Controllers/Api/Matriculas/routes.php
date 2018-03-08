<?php

Route::prefix('matriculas')->middleware(['jwt.auth'])->group(function () {
    Route::get('recuento/{cicloNombre}', 'Api\Matriculas\Matriculas@recuento');
    Route::get('reset', 'Api\Matriculas\Matriculas@resetMatriculaYPlazas');
});



