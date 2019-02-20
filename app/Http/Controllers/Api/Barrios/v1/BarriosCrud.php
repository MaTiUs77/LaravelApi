<?php

namespace App\Http\Controllers\Api\Barrios\v1;

use App\Barrios;
use App\Http\Controllers\Api\Utilities\WithOnDemand;
use App\Http\Controllers\Controller;

class BarriosCrud extends Controller
{
    public function index()
    {
        // Adjunta relaciones a demanda con el parametro "with"
        $with = WithOnDemand::set([], request('with'));
        $query = Barrios::with($with);

        $response = $query->get();

        return $response;
    }
}
