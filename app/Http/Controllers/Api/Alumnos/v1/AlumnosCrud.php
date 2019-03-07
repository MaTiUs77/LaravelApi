<?php

namespace App\Http\Controllers\Api\Alumnos\v1;

use App\Alumnos;
use App\Http\Controllers\Controller;

class AlumnosCrud extends Controller
{
    public function index()
    {
        return Alumnos::customPagination();
    }

    public function show($id)
    {
        return Alumnos::findOrFail($id);
    }
}
