<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AlumnoFamiliarPublicResource_03 extends Resource
{
    public function toArray($request)
    {
        $alumnos_familiars = $this;
        $response = [
            'id' => $alumnos_familiars['id'],
            'alumno_id' => $alumnos_familiars['alumno_id'],
            'familiar_id' => $alumnos_familiars['familiar_id'],
            'documento_nro' => $alumnos_familiars['alumno']['persona']['documento_nro'],
            'nombres' => $alumnos_familiars['alumno']['persona']['nombres'],
            'apellidos' => $alumnos_familiars['alumno']['persona']['apellidos'],
            'status' => $alumnos_familiars['status']

        ];

        return $response;
    }
}