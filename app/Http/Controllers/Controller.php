<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use League\StatsD\Laravel\Facade\StatsdFacade;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

/*
    public function callAction($method, $parameters)
    {
        // Remove App\Http\Controller
        // Split remaining namespace into folders
        $className = str_replace('\\', '.', get_class($this));
        $timerName = sprintf('controller.%s.%s', $className, $method);

        $timer = StatsdFacade::startTiming($timerName);
        $response = parent::callAction($method, $parameters);
        $timer->endTiming($timerName);

        return $response;
    }
*/
}
