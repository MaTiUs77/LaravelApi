<?php
Route::prefix('v1')->group(function () {
    Route::get('exportar/excel/ListaAlumnos', 'Api\Exportar\v1\Exportar@ListaAlumnos');
});
