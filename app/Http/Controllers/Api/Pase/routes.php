<?php
// Rutas con autentificacion
//Route::group(['middleware' => 'jwt.auth'],function () {
    Route::resource('/pase', 'Api\Pase\PaseCrud');
//});



