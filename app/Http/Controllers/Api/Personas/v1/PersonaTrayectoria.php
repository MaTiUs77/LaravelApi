<?php

namespace App\Http\Controllers\Api\Personas\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;

use App\Personas;

class PersonaTrayectoria extends Controller
{
    public function index(Personas $persona)
    {
        $params = [
            'render' => 'trayectoria'
        ];
        // Consumo API Inscripciones
        $apiPersona= new ApiConsume();
        $apiPersona->get("personas/{$persona->id}",$params);

        if($apiPersona->hasError()) { return $apiPersona->getError(); }

        return $apiPersona->response();
    }
}