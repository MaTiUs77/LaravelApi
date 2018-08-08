<?php
Route::prefix('forms')->group(function () {
    Route::get('centros', 'Api\Forms\Forms@centros');
    Route::get('ciclos', 'Api\Forms\Forms@ciclos');
    Route::get('ciudades', 'Api\Forms\Forms@ciudades');
    Route::get('sectores', 'Api\Forms\Forms@sectores');
    Route::get('niveles', 'Api\Forms\Forms@niveles');

    Route::get('años', 'Api\Forms\Forms@años');
    Route::get('divisiones', 'Api\Forms\Forms@divisiones');
    Route::get('turnos', 'Api\Forms\Forms@turnos');
    Route::get('tipos', 'Api\Forms\Forms@tipos');
    Route::get('estado_inscripcion', 'Api\Forms\Forms@estado_inscripcion');
});

