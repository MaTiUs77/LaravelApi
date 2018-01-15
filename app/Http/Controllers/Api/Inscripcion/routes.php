<?php
Route::prefix('inscripcion')->group(function () {
    Route::get('info/{inscripcion_id}', 'Api\Inscripcion\Inscripcion@info');
    Route::get('lista', 'Api\Inscripcion\Inscripcion@lista');

    Route::post('add','Api\Inscripcion\Inscripcion@add');

    Route::prefix('export')->group(function () {
        Route::get('excel', 'Api\Inscripcion\Inscripcion@exportExcel');
    });
});

