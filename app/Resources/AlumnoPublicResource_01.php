<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AlumnoPublicResource_01 extends Resource
{
    public function toArray($request)
    {
        $alumno = $this;
        $response = [
            'id' => $alumno['id'],
            'persona_id' => $alumno['persona_id'],
        ];

        return $response;
    }
}