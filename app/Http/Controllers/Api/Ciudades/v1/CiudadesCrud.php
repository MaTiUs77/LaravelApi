<?php

namespace App\Http\Controllers\Api\Ciudades\v1;

use App\Ciudades;
use App\Http\Controllers\Api\Utilities\WithOnDemand;
use App\Http\Controllers\Controller;

class CiudadesCrud extends Controller
{
    public function index()
    {
        $query = Ciudades::withOnDemand()
            ->select(['id','nombre','departamento_id']);

        $ciudades = $query->get();

        return $ciudades;
    }
}
