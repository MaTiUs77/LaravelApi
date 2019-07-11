<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ContactoPublicResource_01 extends Resource
{
    public function toArray($request)
    {
        $contacto = $this;
        $response = [
            'id' => $contacto['id'],
            'user_social_id' => $contacto['user_social_id'],
            'origin' => $contacto['origin']
        ];

        return $response;
    }
}