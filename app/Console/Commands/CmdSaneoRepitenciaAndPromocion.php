<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Saneo\SaneoRepitencia;
use App\Jobs\JobSaneoRepitenciaAndPromocion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CmdSaneoRepitenciaAndPromocion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'siep:saneo_rp {ciclo} {page} {por_pagina?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza un saneo de las repitencias y promociones';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ciclo = $this->argument('ciclo');
        $por_pagina = $this->argument('por_pagina');
        $page = $this->argument('page');

        $this->info("JobSaneoRepitenciaAndPromocion: $ciclo, $page, $por_pagina");

        //- Procesamos el saneo de la primer pagina -
        $saneo = new SaneoRepitencia();
        $saneo = $saneo->start($ciclo,$page,$por_pagina);

        // Obtenemos ultima pagina
        $ultimaPagina = $saneo['last_page'];
        $nextPage = $page + 1;
        //-------------------------------------------

        $this->info("ARTISAN JobSaneoRepitenciaAndPromocion current:$page / while(nextPage:$nextPage <= last:$ultimaPagina) ");

        Log::info("ARTISAN CmdSaneoRepitenciaAndPromocion: Prepare Jobs");
        while($nextPage <= $ultimaPagina) {
            $this->info("ARTISAN ($nextPage de $ultimaPagina) JobSaneoRepitenciaAndPromocion::dispatch($ciclo,$nextPage,$por_pagina)");
            JobSaneoRepitenciaAndPromocion::dispatch($ciclo,$nextPage,$por_pagina)->delay(now()->addMinutes(1));
            $nextPage++;
        }
        Log::info("ARTISAN CmdSaneoRepitenciaAndPromocion: Jobs Created (Total: $nextPage)");
    }
}
