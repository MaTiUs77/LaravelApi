<?php

namespace App\Http\Controllers\Api\Barrios\v1;

use App\Barrios;
use App\Http\Controllers\Api\Utilities\WithOnDemand;
use App\Http\Controllers\Controller;
use App\Ciudades;

class BarriosCrud extends Controller
{
    public function index()
    {
        $query = Barrios::withOnDemand(['ciudad']);

        $query->when(request('ciudad_id'), function ($q, $v) {
            return $q->where('ciudad_id', $v);
        });

        $query->when(request('ciudad'), function ($q, $v) {
            $ciudad = Ciudades::where('nombre',$v)->firstOrFail();
            return $q->where('ciudad_id', $ciudad->id);
        });

        $query->when(request('nombre'), function ($q, $v) {
            return $q->where('nombre','like', '%'.$v.'%');
        });
        $response = $query->get();
        return $response;
    }
}
