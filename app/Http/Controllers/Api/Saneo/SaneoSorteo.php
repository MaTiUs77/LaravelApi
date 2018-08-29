<?php
namespace App\Http\Controllers\Api\Saneo;

use App\Http\Controllers\Controller;
use App\Inscripcions;
use GuzzleHttp\Client;

class SaneoSorteo extends Controller
{
    public function __construct()
    {
        //$this->middleware('jwt');
    }

    public function start($nivel_servicio='Inicial')
    {
        // Consume API lista de inscripciones
        $guzzle = new Client();
        $consumeApi = $guzzle->get(env('SIEP_LARAVEL_API')."/api/inscripcion/lista",[
            'query' => [
                'por_pagina' => 'all',
                'ciclo' => 2019,
                'estado_inscripcion' => 'NO CONFIRMADA',
                'nivel_servicio' => "Común - $nivel_servicio",
            ]
        ]);

        // Obtiene el contenido de la respuesta, la transforma a json
        $content = $consumeApi->getBody()->getContents();
        $lista = json_decode($content,true);

        // Si no esta definido el error, procedemos a formatear los datos
        if(!isset($lista['error']))
        {
            // Transforma los datos a collection para realizar un mapeo
            $data = collect($lista['data']);

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

            $lista['data'] = $formatted;

            return $lista;
        }

        return $lista;
    }
}
