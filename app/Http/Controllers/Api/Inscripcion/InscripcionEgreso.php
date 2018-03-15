<?php
namespace App\Http\Controllers\Api\Inscripcion;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InscripcionEgreso extends Controller
{
    public $validationRules = [
        'id' => 'required|array',
        'user_id' => 'required|numeric',
    ];

    public function start(Request $request)
    {
        // Se validan los parametros
        $validator = Validator::make($request->all(), $this->validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $this->user =  User::where('id',$request->get('user_id'))->first();
        $cursoInscripcion =  CursosInscripcions::whereIn('inscripcion_id',$request->get('id'))->get();

        $error = [];
        $success = [];
        foreach($cursoInscripcion as $curins)
        {
            $inscripcion = $curins->inscripcion;

            if($inscripcion->fecha_egreso == null){
                if($this->puedeEgresar($curins))
                {
                    $inscripcion->fecha_egreso = Carbon::now();
                    $inscripcion->estado_inscripcion = "EGRESO";
                    $inscripcion->save();
                    $success[$inscripcion->id] = "success";
                } else {
                    $error[$inscripcion->id] = "No puede egresar";
                }
            } else {
                $error[$inscripcion->id] = "Ya se encuentra egresado";
            }
        }

        $output = [
            'success'=>$success,
            'error'=>$error
        ];

        Log::info("Egreso:",$output);

        return $output;
    }

    private function puedeEgresar(CursosInscripcions $curi) {
        $egresar = false;

        $nivelServicio = $curi->inscripcion->centro->nivel_servicio;
        $anio = $curi->curso->anio;

        switch ($nivelServicio)
        {
            case 'Común - Primario':
                if($anio=='6to') { $egresar = true; }
                break;
            case 'Común - Secundario':
                if($anio=='6to') { $egresar = true; }
                break;
            case 'Común - Inicial':
                if($anio=='Sala de 5 años') { $egresar = true; }
                break;
        }

        return $egresar;
    }
}
