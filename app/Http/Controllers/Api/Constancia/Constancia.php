<?php

namespace App\Http\Controllers\Api\Constancia;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use App\User;

use Barryvdh\DomPDF\Facade as PDF;

class Constancia extends Controller
{
    public function generarPdf($inscripcion_id)
    {
        if(is_numeric($inscripcion_id))
        {
            $cursoInscripcions = CursosInscripcions::with([
                'Curso.Centro.Barrio',
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
                $pdf = PDF::loadView('constancia',array('cursoInscripcions'=>$cursoInscripcions));
                return $pdf->stream('constancia.pdf');
            }
        } else
        {
            return ['error'=>'El ID es inválido'];
        }
    }
}