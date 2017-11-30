<?php

namespace App\Http\Controllers\Api\Inscripcion;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;

class Inscripcion extends Controller
{
    public function info($inscripcion_id)
    {
        if(is_numeric($inscripcion_id))
        {
            $cursoInscripcions = CursosInscripcions::with([
                'Curso.Centro.Barrio',
                'Curso.Centro.Ciudad',
                'Inscripcion.Ciclo',
                'Inscripcion.Alumno.Persona.Ciudad',
                'Inscripcion.Hermano.Persona.Ciudad',
            ])
                ->where('inscripcion_id',$inscripcion_id)
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
}