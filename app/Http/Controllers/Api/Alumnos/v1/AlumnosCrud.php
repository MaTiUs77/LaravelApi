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
        $alumnos = Alumnos::withOnDemand();
        $alumnos->when(request('id'), function ($q, $v) {
            return $q->findOrFail($v);
        });

        $alumnos->when(request('persona_id'), function ($q, $v) {
            return $q->where('persona_id',$v);
        });

        return $alumnos->customPagination();
    }

    public function show($id)
    {
        return Alumnos::withOnDemand()->findOrFail($id);
    }

    // Create
    public function store(AlumnosCrudStoreReq $req)
    {
        // Verificar existencia del familiar, segun persona_id
        $alumno = Alumnos::where('persona_id',request('persona_id'))->orderBy('id','desc')->first();
        // Si no existe el alumno... crea el alumno
        if(!$alumno) {
            // Se crea el alumno
            $alumno = Alumnos::create($req->all());
        }

        return compact('alumno');
    }

    // Busca un familiar por persona_id
    public function getByPersonaId($persona_id)
    {
        $familiar = Alumnos::where('persona_id',$persona_id)->orderBy('id','desc')->first();
        return $familiar;
    }
}
