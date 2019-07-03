<?php

namespace App\Http\Controllers\Api\Alumnos\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
use App\Resources\ListaAlumnosResource;

class AlumnosContacto extends Controller
{
    public function index($ciclo_id,$centro_id,$curso_id)
    {
        $params = request()->all();
        $params['ciclo'] = $ciclo_id;
        $params['centro_id'] = $centro_id;
        $params['curso_id'] = $curso_id;
        $params['estado_inscripcion'] = 'CONFIRMADA';
        $params['with'] = 'inscripcion.alumno.familiares.familiar.persona';
        $params['por_pagina'] = 'all';

        // Consumo API Inscripciones
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        // Prepara un collection y ordena por apellido
        $data = collect($response['data']);
        $sorted = $data->sortBy('inscripcion.alumno.persona.nombre_completo')->values();

        $first  = $data->first();
        $ciclo = $first['inscripcion']['ciclo'];
        $centro = $first['inscripcion']['centro'];
        $curso= $first['curso'];

        // Transforma el resultado, con un formato pre-establecido por el resource
        $alumnos = ListaAlumnosResource::collection(collect($sorted));
        return compact('ciclo','centro','curso','alumnos');
        
    }
}
