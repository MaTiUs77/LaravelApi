<?php

namespace App\Http\Controllers\Api\Exportar\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Api\Utilities\Export;
use App\Http\Controllers\Controller;
use App\Resources\ListaAlumnosResource;

class Exportar extends Controller
{
    public function ListaAlumnos() {
        $params = request()->all();

        // Consumo API Inscripciones
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        // Prepara un collection y ordena por apellido
        $data = collect($response['data']);
        $sorted = $data->sortBy('inscripcion.alumno.persona.nombre_completo')->values();

        // Transforma el resultado, con un formato pre-establecido por el resource
        $alumnos = ListaAlumnosResource::collection(collect($sorted));

        if(count($alumnos)>0)
        {
            Export::resourceToExcel('ListaAlumnos','Lista de Alumnos',$alumnos);
        } else {
            return ['error' => 'No se obtuvieron resultados con el filtro aplicado'];
        }
    }
}