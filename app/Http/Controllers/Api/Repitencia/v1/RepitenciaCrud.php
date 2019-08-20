<?php

namespace App\Http\Controllers\Api\Repitencia\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;

class RepitenciaCrud extends Controller
{
    public function index()
    {
        $params = request()->all();
        $default['transform'] = 'RepitenciaResource';
        $default['repitencia'] = 'con';
        $default['with'] = 'inscripcion.repitencia';
/*
        $default['estado_inscripcion'] = 'CONFIRMADA';
        $default['nivel_servicio'] = [
            'Comun - Primario',
            'Comun - Secundario',
        ];*/
        $params = array_merge($params,$default);

        // Consumo API Personas
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);

        if($api->hasError()) { return $api->getError(); }

        return $api->response();
    }

    public function store() {
        $repitencia = new RepitenciaStore();
        return $repitencia->start(request());
    }
}