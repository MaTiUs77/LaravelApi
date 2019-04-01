<?php

namespace App\Http\Controllers\Api\Alumnos\v1;

use App\Http\Controllers\Api\Alumnos\v1\Request\AlumnosCrudIndexReq;
use App\Http\Controllers\Api\Alumnos\v1\Request\AlumnosCrudStoreReq;
use App\Http\Controllers\Api\Alumnos\v1\Request\AlumnosCrudUpdateReq;
use App\Http\Controllers\Api\Utilities\DefaultValidator;

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

    // Create
    public function store(AlumnosCrudStoreReq $req)
    {
        // Verificar existencia del familiar, segun persona_id
        $alumno = Alumnos::where('persona_id',request('persona_id'))->first();

        // Si no existe el alumno... crea el alumno
        if(!$alumno) {
            // Se crea el alumno
            $alumno = Alumnos::create($req->all());
        }

        return compact('alumno');
    }
}
