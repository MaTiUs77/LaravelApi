<?php

namespace App\Http\Controllers\Api\Ciudades\v1;

use App\Ciudades;
use App\Http\Controllers\Api\Utilities\WithOnDemand;
use App\Http\Controllers\Controller;

class CiudadesCrud extends Controller
{
    public function index()
    {
        // Adjunta relaciones a demanda con el parametro "with"
        $with = WithOnDemand::set([], request('with'));
        $query = Ciudades::select(['id','nombre','departamento_id'])->with($with);

        $ciudades = $query->get();

        return $ciudades;
    }
}
