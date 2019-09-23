<?php
namespace App\Http\Controllers\Api\Saneo\v1;

use App\Http\Controllers\Controller;
use App\Jobs\JobSaneoEdad;
use App\Personas;
use Illuminate\Support\Facades\Log;

class SaneoEdad extends Controller
{
    public function start()
    {
        $por_pagina = 100;
        $personas = Personas::paginate($por_pagina);
        $ultimaPagina = $personas->lastPage();
     
        $nextPage= 1;
        while($nextPage<=$ultimaPagina) {
            Log::info("ARTISAN JobSaneoEdad::dispatch: $nextPage / $ultimaPagina");
            JobSaneoEdad::dispatch($nextPage,$ultimaPagina)->delay(now()->addMinutes(2));
            $nextPage++;
        }
        Log::info("ARTISAN JobSaneoEdad: Jobs Created $ultimaPagina");

        return [
            'job' => 'JobSaneoEdad',
            'totalPage' => $ultimaPagina
        ];
    }
}
