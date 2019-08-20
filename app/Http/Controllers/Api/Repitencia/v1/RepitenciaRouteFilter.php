<?php
namespace App\Http\Controllers\Api\Repitencia\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;

class RepitenciaRouteFilter extends Controller
{
    public function index($ciclo, $centro_id=null, $curso_id=null)
    {
        $params = request()->all();
        $default['ciclo'] = $ciclo;
        $default['centro_id'] = $centro_id;
        $default['curso_id'] = $curso_id;

        $params = array_merge($params,$default);

        // Consumo API Inscripciones
        $apiPersona= new ApiConsume();
        $apiPersona->get("repitencia",$params);
        if($apiPersona->hasError()) { return $apiPersona->getError(); }

        return $apiPersona->response();
    }
}
