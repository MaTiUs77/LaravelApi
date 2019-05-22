<?php

namespace App\Http\Controllers\Api\Artisan\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ArtisanRouteCommand extends Controller
{
    public function repitencia()
    {
        Log::info("ROUTE /api/v1/artisan/repitencia");

        $artisan = Artisan::call('siep:saneo_rp', [
            'ciclo' => 2019,
            'por_pagina' => 20,
            'page' => 1
        ]);

        $status = 'Artisan::call';
        return compact('status','artisan');

    }

    public function migrate() {
        $artisan = Artisan::call('migrate');

        $status = 'Artisan::migrate';
        return compact('status','artisan');
    }

    public function log($file) {
        $file ="laravel-$file.log";
        return response()->download(storage_path("logs/{$file}"));
    }
}
