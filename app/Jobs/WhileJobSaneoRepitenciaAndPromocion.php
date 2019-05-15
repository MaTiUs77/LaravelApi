<?php

namespace App\Jobs;
ini_set('max_execution_time', '3');

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class WhileJobSaneoRepitenciaAndPromocion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ciclo;
    protected $por_pagina;
    protected $page;
    protected $ultima_pagina;

    protected $build;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ciclo=2019,$page=1,$por_pagina=10,$ultima_pagina=null)
    {
        $this->build = 2;

        $this->ciclo= $ciclo;
        $this->page = $page;
        $this->por_pagina= $por_pagina;
        $this->ultima_pagina= $ultima_pagina;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $nextPage = $this->page + 1;
        Log::info("ARTISAN WhileJobSaneoRepitenciaAndPromocion: Build {$this->build}");
        Log::info("ARTISAN WhileJobSaneoRepitenciaAndPromocion: Prepare Jobs $nextPage/{$this->ultima_pagina}");
        while($nextPage < $this->ultima_pagina) {
            Log::info("ARTISAN JobSaneoRepitenciaAndPromocion::dispatch: $nextPage");
            JobSaneoRepitenciaAndPromocion::dispatch($this->ciclo,$nextPage,$this->por_pagina)->delay(now()->addMinutes(10));
            $nextPage++;
        }
        Log::info("ARTISAN WhileJobSaneoRepitenciaAndPromocion: Jobs Created (Total: $nextPage / {$this->ultima_pagina})");
    }
}