<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Saneo\SaneoRepitencia;
use App\Jobs\JobSaneoRepitenciaAndPromocion;
use App\Jobs\WhileJobSaneoRepitenciaAndPromocion;
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
        set_time_limit(0);
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
        //-------------------------------------------

        WhileJobSaneoRepitenciaAndPromocion::dispatch($ciclo,$page,$por_pagina,$ultimaPagina);

        $this->info("ARTISAN CmdSaneoRepitenciaAndPromocion ciclo: $ciclo / current:$page / por_pagina: $por_pagina / while(page:$page <= last:$ultimaPagina) ");
        Log::info("ARTISAN CmdSaneoRepitenciaAndPromocion ciclo: $ciclo / current:$page / por_pagina: $por_pagina / while(page:$page <= last:$ultimaPagina) ");
    }
}
