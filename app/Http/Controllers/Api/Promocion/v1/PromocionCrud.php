<?php

namespace App\Http\Controllers\Api\Promocion\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;

use App\Resources\PromocionResource;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;

class PromocionCrud extends Controller
{
    public function index()
    {
        $params = request()->all();
        $default['transform'] = 'PromocionResource';
        $default['estado_inscripcion'] = 'CONFIRMADA';
        $default['with'] = 'inscripcion.promocion';
        $default['nivel_servicio'] = [
            'Comun - Primario',
            'Comun - Secundario',
        ];
        $params = array_merge($params,$default);

        // Consumo API Personas
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);

        if($api->hasError()) { return $api->getError(); }

        return $api->response();
    }
}