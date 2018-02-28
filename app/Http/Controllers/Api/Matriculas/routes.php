<?php
Route::prefix('matriculas')->group(function () {
    Route::get('recuento/{cicloNombre}', 'Api\Matriculas\Matriculas@recuento');
    Route::get('reset', 'Api\Matriculas\Matriculas@resetMatriculaYPlazas');
});