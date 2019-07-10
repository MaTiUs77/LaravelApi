<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FamiliarPublicResource_02 extends Resource
{
    public function toArray($request)
    {
        $familiar = $this;

        return $familiar;
    }
}