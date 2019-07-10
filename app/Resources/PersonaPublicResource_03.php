<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PersonaPublicResource_03 extends Resource
{
    public function toArray($request)
    {
        $persona = $this;

        return $persona;
    }
}