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
        $query = AlumnosFamiliar::withOnDemand();

        $query->when(request('status'), function ($q, $v) {
            return $q->where('status', $v);
        });

        $query->when(request('alumno_id'), function ($q, $v) {
            return $q->where('alumno_id',$v);
        });

        $query->when(request('familiar_id'), function ($q, $v) {
            return $q->where('familiar_id',$v);
        });

        $result = $query->customPagination();

        return $result;
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
        $alumnos_familiars = AlumnosFamiliar::where('alumno_id',request('alumno_id'))
                                            ->where('familiar_id',request('familiar_id'))->first();
        // Si no existe el alumno... crea el alumno
        if(!$alumnos_familiars) {
            // Se crea la relación
            $alumnos_familiars = AlumnosFamiliar::create($req->all());
        }

        return compact('alumnos_familiars');
    }

    // Update
    public function update($id, AlumnosFamiliarsCrudUpdateReq $req)
    {
        $relacion = AlumnosFamiliar::findOrFail($id);
        switch(request('mode')) {
            case 'confirmar':
                $relacion->status = 'confirmada';
                break;
            case 'cancelar':
                $relacion->status = 'revisar';
                break;
            case 'pendiente':
                $relacion->status = 'pendiente';
                break;
        };

        $relacion->save();

        return $relacion;
    }

    // Busca los Alumnos para un familiar
    public function getByFamiliar($familiar_id)
    {
        $alumnos = AlumnosFamiliar::withOnDemand(['alumno.persona'])->where('familiar_id',$familiar_id)->get();
        return $alumnos;
    }
}
