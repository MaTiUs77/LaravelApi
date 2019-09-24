<?php

Route::prefix('v1')->group(function () {
    Route::resource('administracion', 'Api\Administracion\v1\AdministracionCrud');
});
