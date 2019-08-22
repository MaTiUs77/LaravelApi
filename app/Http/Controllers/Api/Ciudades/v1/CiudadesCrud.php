<?php

namespace App\Http\Controllers\Api\Ciudades\v1;

use App\Ciudades;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Utilities\DefaultValidator;

class CiudadesCrud extends Controller
{
    public function index()
    {
        $input = request()->all();
        $rules = [
            'nombre' => 'string'
        ];
        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        $query = Ciudades::withOnDemand()
            ->select(['id','nombre','departamento_id']);
        
        $query->when(request('nombre'), function ($q, $v) {
            return $q->where('nombre', $v);
        });

        $query->when(request('departamento_id'), function ($q, $v) {
            return $q->where('departamento_id', $v);
        });

        $ciudades = $query->get();

        if($ciudades->isNotEmpty()) {
            return $ciudades;
        } else {
            abort(204,'No se encontraron resultados con el filtro aplicado');
        }

        return $ciudades;
    }
}
