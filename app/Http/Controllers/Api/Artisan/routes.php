<?php

// V1
Route::prefix('v1')->group(function () {
    Route::prefix('artisan')->group(function () {

        Route::get('repitencia', function(){
            $artisan = Artisan::call('siep:saneo_rp', [
                'ciclo' => 2019,
                'por_pagina' => 50,
                'page' => 1
            ]);

            $status = 'Artisan::call';
            return compact('status','artisan');
        });

        Route::get('migrate', function () {
            $artisan = Artisan::call('migrate');

            $status = 'Artisan::migrate';
            return compact('status','artisan');
        });

    });
});
