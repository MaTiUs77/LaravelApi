<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AlumnoFamiliarPublicResource_02 extends Resource
{
    public function toArray($request)
    {
        $alumno_familiar = $this;

        return $alumno_familiar;
    }
}