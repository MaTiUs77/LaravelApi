<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FamiliarPublicResource_01 extends Resource
{
    public function toArray($request)
    {
        $familiar = $this;
        $response = [
            'id' => $familiar['id'],
            'persona_id' => $familiar['persona_id']
        ];

        return $response;
    }
}