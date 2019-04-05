<?php

namespace App\Http\Controllers\Api\AlumnosFamiliars\v1;

use App\Http\Controllers\Api\AlumnosFamiliars\v1\Request\AlumnosFamiliarsCrudIndexReq;
use App\Http\Controllers\Api\AlumnosFamiliars\v1\Request\AlumnosFamiliarsCrudStoreReq;
use App\Http\Controllers\Api\AlumnosFamiliars\v1\Request\AlumnosFamiliarsCrudUpdateReq;
use App\Http\Controllers\Api\Utilities\DefaultValidator;

use App\AlumnosFamiliar;
use App\Http\Controllers\Controller;

class AlumnosFamiliarsCrud extends Controller
{
    public function index()
    {
        return AlumnosFamiliar::withOnDemand()->customPagination();
    }

    public function show($id)
    {
        return AlumnosFamiliar::withOnDemand()->findOrFail($id);
    }

    // Create
    public function store(AlumnosFamiliarsCrudStoreReq $req)
    {
        // 
        // Verificar existencia del familiar, segun persona_id
        $alumnos_familiars = AlumnosFamiliar::where('alumno_id',request('alumno_id'))->first();
        // Si no existe el alumno... crea el alumno
        if(!$alumnos_familiars) {
            // Se crea la relación
            $alumnos_familiars = AlumnosFamiliar::create($req->all());
        }

        return compact('alumnos_familiars');
    }
}
