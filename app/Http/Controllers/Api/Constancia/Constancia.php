<?php

namespace App\Http\Controllers\Api\Constancia;

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

    public function generarPdf($inscripcion_id)
    {
        $params = ['inscripcion_id'=>$inscripcion_id];
        $validator = Validator::make($params, $this->validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $cursoInscripcions = CursosInscripcions::where('inscripcion_id',$inscripcion_id)->first();

        if(!$cursoInscripcions)
        {
            return ['error'=>'No se encontro una inscripcion con esa ID'];
        } else {

            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                ->loadView('constancia',array('cursoInscripcions'=>$cursoInscripcions));

            return $pdf->stream("constancia_$inscripcion_id.pdf");
        }
    }
}