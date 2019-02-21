<?php

namespace App\Http\Controllers\Api\Constancia\v1;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade as PDF;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class Constancia extends Controller
{
    public $validationRules = [
        'inscripcion_id' => 'required|numeric'
    ];

    public function inscripcion($inscripcion_id)
    {
        // Se validan los parametros
        $input = ['inscripcion_id'=>$inscripcion_id];
        $validator = Validator::make($input,$this->validationRules);

        if ($validator->fails()) {
            return [
                'error_type' => 'ValidationException',
                'error' => $validator->errors()
            ];
        }

        $cursoInscripcions = CursosInscripcions::findOrFail($inscripcion_id);

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('constancia_inscripcion',array('cursoInscripcions'=>$cursoInscripcions));

        return $pdf->stream("constancia_inscripcion_$inscripcion_id.pdf");
    }

    public function regular($inscripcion_id)
    {
        // Se validan los parametros
        $input = ['inscripcion_id'=>$inscripcion_id];
        $validator = Validator::make($input,$this->validationRules);

        if ($validator->fails()) {
            return [
                'error_type' => 'ValidationException',
                'error' => $validator->errors()
            ];
        }

        $cursoInscripcions = CursosInscripcions::findOrFail($inscripcion_id);

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('constancia_regular',array('cursoInscripcions'=>$cursoInscripcions));

        return $pdf->stream("constancia_regular_$inscripcion_id.pdf");
    }
}