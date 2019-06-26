<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PersonaPublicResource extends Resource
{
    public function toArray($request)
    {
        $persona = $this;
        $response = [
            'documento_tipo' => $persona['documento_tipo'],
            'documento_nro' => $persona['documento_nro'],
        ];

        return $response;
    }
}