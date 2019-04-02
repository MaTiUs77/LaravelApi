<?php

namespace App\Jobs;

use App\Http\Controllers\Api\Saneo\SaneoRepitencia;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class JobSaneoRepitenciaAndPromocion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page=1)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("JobSaneoRepitenciaAndPromocion($this->page) -- START");
        $domagic = new SaneoRepitencia();
        $domagic->start($this->page);
        Log::info("JobSaneoRepitenciaAndPromocion($this->page) -- COMPLETE");
    }
}
