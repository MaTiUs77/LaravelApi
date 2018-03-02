<?php
namespace App\Http\Controllers\Api\Inscripcion;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class InscripcionFind extends Controller
{
    public $validationRules = [
        'centro_id' => 'required|numeric',
        'ciclo_id' => 'required|numeric',
        'turno' => 'string',
        'anio' => 'string',
        'por_pagina' => 'numeric',
        'con_hermano' => 'sometimes|accepted',
    ];

    public $validationMessages = [
        'required' => 'El :attribute es requerido.',
        'numeric' => 'El :attribute debe ser numerico',
        'string' => 'El :attribute debe ser solo texto',
        'accepted' => 'El :attribute debe ser: 1, on o true',
    ];

    public function byId($inscripcion_id)
    {
        if(is_numeric($inscripcion_id))
        {
            $cursoInscripcions = CursosInscripcions::where('inscripcion_id',$inscripcion_id)
//                ->with('Inscripcion.Hermano.Persona.Ciudad')
                ->first();

            if(!$cursoInscripcions)
            {
                return ['error'=>'No se encontro una inscripcion con esa ID'];
            } else {

                return $cursoInscripcions;
            }
        } else
        {
            return ['error'=>'El ID es inválido'];
        }
    }

    public function byLegajo($legajo_nro)
    {
        list($dni,$anio) = explode('-',$legajo_nro);
        if(is_numeric($dni) && is_numeric($anio))
        {
            $inscripcion = Inscripcions::where('legajo_nro',$legajo_nro)->first();

            // Verifica si existe ese legajo en las inscripciones
            if($inscripcion!=null) {
                $cursoInscripcions = CursosInscripcions::where('inscripcion_id',$inscripcion->id)
                    ->first();

                if($cursoInscripcions)
                {
                    return $cursoInscripcions;
                } else {
                    return ['error'=>'No se encontro una inscripcion con ese legajo'];
                }
            } else {
                return ['error'=>'No se encontro una inscripcion con ese numero de legajo'];
            }
        } else
        {
            return ['error'=>'El legajo es inválido'];
        }
    }
}
