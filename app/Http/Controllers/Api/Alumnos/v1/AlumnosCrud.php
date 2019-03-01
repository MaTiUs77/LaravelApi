<?php

namespace App\Http\Controllers\Api\Alumnos\v1;

use App\Alumnos;
use App\Http\Controllers\Api\Utilities\WithOnDemand;
use App\Http\Controllers\Controller;

class AlumnosCrud extends Controller
{
    public function index()
    {
        // Adjunta relaciones a demanda con el parametro "with"
        $with = WithOnDemand::set([], request('with'));
        $query = Alumnos::with($with);

        $result = $query->get();

        return $result;
    }

    public function show($id)
    {
        // Adjunta relaciones a demanda con el parametro "with"
        $with = WithOnDemand::set([], request('with'));
        $alumno = Alumnos::with($with)->findOrFail($id);

        return $alumno;
    }
}
