<?php

namespace App\Jobs;

use App\Http\Controllers\Api\Saneo\SaneoEdad;
use App\Personas;
use Illuminate\Bus\Queueable;
use Illuminate\Pagination\Paginator;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class JobSaneoEdad implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $por_pagina;
    protected $page;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page=1,$por_pagina=10)
    {
        $this->por_pagina= $por_pagina;
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentPage = $this->page;
        Paginator::currentPageResolver(function() use ($currentPage) {
            return $currentPage;
        });

        Log::info("HANDLE JobSaneoEdad($this->page,$this->por_pagina) -- START");
        $personas = Personas::paginate($this->por_pagina);
        foreach ($personas as $persona) {
            $this->fixEdad($persona);
        }
        Log::info("HANDLE JobSaneoEdad($this->page,$this->por_pagina) -- COMPLETE");
    }

    public function fixEdad(Personas $persona) {
        $edadActual = Carbon::parse($persona->fecha_nac)->age;
        $persona->edad = $edadActual;
        $persona->save();
    }
}
