<?php

namespace App\Http\Controllers\Api\Personas\v1;

use App\CursosInscripcions;
use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;

use App\Personas;

class PersonaUltimaInscripcion extends Controller
{
    public function index(Personas $persona)
    {
        $params = [
            'with' => 'alumnos'
        ];
        // Consumo API Inscripciones
        $apiPersona= new ApiConsume();
        $apiPersona->get("personas/{$persona->id}",$params);

        if($apiPersona->hasError()) { return $apiPersona->getError(); }

        return $apiPersona->response();
    }
}