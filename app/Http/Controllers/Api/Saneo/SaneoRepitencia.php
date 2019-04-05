<?php
namespace App\Http\Controllers\Api\Saneo;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SaneoRepitencia extends Controller
{
    public function start($ciclo=2019,$page=1,$por_pagina=10)
    {
        Log::info("=============================================================================");
        Log::info("SaneoRepitencia::start($ciclo,$page,$por_pagina)");

        if(request('page')) {
            $page = request('page');
        }

        $params = [
            'transform'=>'RepitentesResource',
            'ciclo' => $ciclo,
            'division' => 'con',
            'estado_inscripcion' => 'CONFIRMADA',
            'nivel_servicio' => ['Comun - Primario','Comun - Secundario'],

            'promocion' => 'sin',
            'repitencia' => 'sin',
//            'anio' => 'Sala de 4 años',
            'por_pagina' => $por_pagina,
            'page' => $page,
        ];

        // Consumo API Inscripciones
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        Log::info("SaneoRepitencia: ".$page." de ".$response['meta']['last_page']);

        /*        $data = collect($response['data']);
                $response = ListaAlumnosResource::collection($data);*/

        return $response;
    }
}
