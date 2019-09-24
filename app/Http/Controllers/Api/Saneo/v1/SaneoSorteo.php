<?php
namespace App\Http\Controllers\Api\Saneo\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use GuzzleHttp\Client;

class SaneoSorteo extends Controller
{
    public function __construct()
    {
        //$this->middleware('jwt');
    }

    public function start($ciclo,$nivel_servicio)
    {
        // Consume API lista de inscripciones
        $params = [
            'por_pagina' => 'all',
            'ciclo' => $ciclo,
            'estado_inscripcion' => 'NO CONFIRMADA',
            'nivel_servicio' => "Común - $nivel_servicio",
        ];

        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);
        if($api->hasError()) { return $api->getError(); }
        $response = $api->response();

        // Si no esta definido el error, procedemos a formatear los datos
        if(!isset($response['error']))
        {
            // Transforma los datos a collection para realizar un mapeo
            $data = collect($response['data']);

            $formatted = $data->map(function($item){
                $inscripcion = $item['inscripcion'];

                $inscripcion_id = $inscripcion['id'];
                $old = [
                    'estado_inscripcion' => $inscripcion['estado_inscripcion'],
                    'legajo_nro' => $inscripcion['legajo_nro'],
                ];

                $new = [
                    'estado_inscripcion' => 'BAJA',
                    'legajo_nro' => $inscripcion['legajo_nro'].'-SINVACANTE_1',
                ];

                $fix = Inscripcions::find($inscripcion_id);
                $fix->update($new);

                return compact('inscripcion_id','old','new','update');
            });

            $response['data'] = $formatted;

            return $response;
        }

        return $response;
    }
}
