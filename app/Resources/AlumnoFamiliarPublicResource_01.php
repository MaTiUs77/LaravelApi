<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AlumnoFamiliarPublicResource_01 extends Resource
{
    public function toArray($request)
    {
        $alumnos_familiars = $this;
        $response = [
            'id' => $alumnos_familiars['id'],
            'alumno_id' => $alumnos_familiars['alumno_id'],
            'familiar_id' => $alumnos_familiars['familiar_id']
        ];

        return $response;
    }
}