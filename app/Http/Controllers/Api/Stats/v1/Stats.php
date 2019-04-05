<?php

namespace App\Http\Controllers\Api\Stats\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;

use League\StatsD\Laravel\Facade\StatsdFacade;

class Stats extends Controller
{
    public function index()
    {
        $bajas19 = $this->total(2019,'BAJA');
        $confirmadas19 = $this->total(2019,'CONFIRMADA');

        $bajas18 = $this->total(2018,'BAJA');
        $confirmadas18 = $this->total(2018,'CONFIRMADA');

        return compact('confirmadas19','bajas19','confirmadas18','bajas18');
    }

    public function total($ciclo,$estado_inscripcion) {
        $params = [
            'ciclo'=>$ciclo,
            'estado_inscripcion'=>$estado_inscripcion,
            'division' => 'con'
        ];
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);
        if($api->hasError()) { return $api->getError(); }
        $inscripciones = $api->response();

        StatsdFacade::gauge("api.stats.$ciclo.inscripciones.$estado_inscripcion", $inscripciones['total']);

        return $inscripciones['total'];
    }
}