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
    protected $signature = 'siep:saneo_rp {page?}';

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
        $page = $this->argument('page');
        if(!$page) { $page = 1; }

        $this->info('JobSaneoRepitenciaAndPromocion: '.$page);

        //- Procesamos el saneo de la primer pagina -
        $saneo = new SaneoRepitencia();
        $saneo = $saneo->start($page);
        // Obtenemos ultima pagina
        $ultimaPagina = $saneo['meta']['last_page'];
        $nextPage = $page + 1;
        //-------------------------------------------

        $this->info("ARTISAN JobSaneoRepitenciaAndPromocion current:$page / while(nextPage:$nextPage <= last:$ultimaPagina) ");

        while($nextPage <= $ultimaPagina) {
            $this->info("ARTISAN ($nextPage de $ultimaPagina) JobSaneoRepitenciaAndPromocion::dispatch($nextPage)");
            JobSaneoRepitenciaAndPromocion::dispatch($nextPage)->delay(now()->addMinutes(10));
            $nextPage++;
        }
    }
}
