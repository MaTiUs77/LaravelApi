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
