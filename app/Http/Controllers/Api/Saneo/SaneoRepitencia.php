<?php
namespace App\Http\Controllers\Api\Saneo;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
use App\Resources\ListaAlumnosResource;

class SaneoRepitencia extends Controller
{
    public function start($page=null)
    {
        if(!$page) {
            $page = request('page');
            if(!$page) {
                $page = 1;
            }
        }

        $params = [
            'transform'=>'ListaAlumnosResource',
            'ciclo' => 2019,
            'division' => 'con',
            'estado_inscripcion' => 'CONFIRMADA',
            'nivel_servicio' => ['Comun - Primario','Comun - Secundario'],

            'promocion' => 'sin',
            'repitencia' => 'sin',
//            'anio' => 'Sala de 4 años',
            'por_pagina' => 10,
            'page' => $page,
        ];

        // Consumo API Inscripciones
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

/*        $data = collect($response['data']);
        $response = ListaAlumnosResource::collection($data);*/

        return $response;
    }
}
