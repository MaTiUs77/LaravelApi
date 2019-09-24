<?php

namespace App\Http\Controllers\Api\Promocion\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;

class PromocionCrud extends Controller
{
    public function index()
    {
        $params = request()->all();
        $default['transform'] = 'PromocionResource';
        $default['promocion'] = 'con';
        $default['with'] = 'inscripcion.promocion';
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

    public function store()
    {
        $promocion = new PromocionStore();
        return $promocion->start(request());
    }
}