<?php

namespace App\Http\Controllers\Api\Alumnos\v1;

use App\Alumnos;
use App\Http\Controllers\Controller;

class AlumnosCrud extends Controller
{
    public function index()
    {
        return Alumnos::withOnDemand()->customPagination();
    }

    public function show($id)
    {
        return Alumnos::withOnDemand()->findOrFail($id);
    }
}
