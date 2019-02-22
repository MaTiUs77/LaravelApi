<?php

namespace App\Http\Controllers\Api\Personas\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade as PDF;

class Ficha extends Controller
{
    public function index($persona_id)
    {
        // Validacion de parametros
        $input = ['persona_id'=>$persona_id];
        $rules = ['persona_id'=>'required|numeric'];
        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        // Consumo API Personas
        $persona = new ApiConsume();
        $persona->get("personas/$persona_id",[
            "with" => "barrio"
        ]);

        if($persona->hasError()) { return $persona->getError(); }

        // Consumo API Inscripciones
        $trayectoria = new ApiConsume();
        $trayectoria->get("inscripcion/find",[
            "persona_id" => $persona_id,
            "with" => "inscripcion.alumno.familiares.persona"
        ]);

        if($trayectoria->hasError()) { return $trayectoria->getError(); }

        $cursoIns = $trayectoria->response();
        $soloFamiliares= collect($cursoIns)->map(function($v){
            return $v['inscripcion']['alumno']['familiares'];
        });

        $familiares = $soloFamiliares->flatten(1)->unique('id');

        // Renderizacion de PDF
        $options = [
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ];
        $pdf = PDF::setOptions($options)->loadView('personas.ficha',[
            'persona' => $persona->response(),
            'trayectoria' => $trayectoria->response(),
            'familiares' => $familiares
        ]);

        return $pdf->stream("persona_ficha_$persona_id.pdf");
    }
}