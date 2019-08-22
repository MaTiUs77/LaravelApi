<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PersonaPublicResource_02 extends Resource
{
    public function toArray($request)
    {
        $persona = $this;
        $response = [
            'id' => $persona['id'],
            'apellidos' => $persona['apellidos'],
            'nombres' => $persona['nombres'],
            'documento_nro' => $persona['documento_nro'],
            'sexo' => $persona['sexo'],
            'alumno' => $persona['alumno'],
            'familiar' => $persona['familiar'],
            'ciudad' => $persona['ciudad'],
            'barrio' => $persona['barrio'],
            'familiares' => $persona['familiar']
        ];

        return $response;
    }
}