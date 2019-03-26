<?php

Route::prefix('v1')->group(function () {
    Route::resource('contacto', 'Api\Contacto\v1\ContactoCrud');
});
