<?php

namespace App\Http\Controllers\Api\Barrios\v1;

use App\Barrios;
use App\Http\Controllers\Api\Utilities\WithOnDemand;
use App\Http\Controllers\Controller;

class BarriosCrud extends Controller
{
    public function index()
    {
        $query = Barrios::withOnDemand();
        $response = $query->get();
        return $response;
    }
}
