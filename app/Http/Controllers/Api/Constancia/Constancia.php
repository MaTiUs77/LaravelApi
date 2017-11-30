<?php

namespace App\Http\Controllers\Api\Constancia;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade as PDF;
use GuzzleHttp\Client;

class Constancia extends Controller
{
    public function test($id)
    {
        $guzzle = new Client();
        return $guzzle->get("http://web:3000/api/inscripcion/$id");
    }

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

                $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                    ->loadView('constancia',array('cursoInscripcions'=>$cursoInscripcions));

                return $pdf->stream("constancia_$inscripcion_id.pdf");
            }
        } else
        {
            return ['error'=>'El ID es inválido'];
        }
    }
}