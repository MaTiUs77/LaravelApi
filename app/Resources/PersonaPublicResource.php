<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PersonaPublicResource extends Resource
{
    public function toArray($request)
    {
        $persona = $this;
        $response = [
            'id' => $persona['id'],
            'documento_nro' => $persona['documento_nro'],
        ];

        return $response;
    }
}