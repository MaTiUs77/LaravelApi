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

        $user =  User::where('id',$request->get('user_id'))->first();
        $cursoInscripcion =  CursosInscripcions::whereIn('inscripcion_id',$request->get('id'))->get();

        $error = [];
        $success = [];

        $allslack = [];

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

                    $slack = $this->slackFormat($inscripcion);
                    $slack['operacion'] = "completa";

                } else {
                    $error[$inscripcion->id] = "No puede egresar";

                    $slack = $this->slackFormat($inscripcion);
                    $slack['operacion'] = "No puede egresar";
                }
            } else {
                $error[$inscripcion->id] = "Ya se encuentra egresado";

                $slack = $this->slackFormat($inscripcion);
                $slack['operacion'] = "Ya se encuentra egresado";
            }

            $allslack[] = $slack;
        }

        $output['success'] = $success;

        if(count($error)>0)
        {
            $output['error'] = $error;
        }

        // SLACK
        Log::channel('siep_desarrollo')
            ->debug("InscripcionEgreso::start()",[
                'user'=>$user->username,
                'centro'=>$user->centro->nombre,
                'IDs para Egreso'=>request('id'),
                'detalle'=>$allslack
        ]);

        return $output;
    }

    private function puedeEgresar(CursosInscripcions $curi) {
        $egresar = false;

        $nivelServicio = $curi->inscripcion->centro->nivel_servicio;
        $cue = $curi->inscripcion->centro->cue;
        $anio = $curi->curso->anio;

        /*
            ESTO NO DEBERIA ESTAR ASI, DEBERIA SER POR ACL
            CON PERMISOS DE EGRESO PARA CADA COLEGIO..

            $anio->can('egresar')

            Todos los 6to egresan PRIMARIOS y SECUNDARIOS
            Menos estos 3 CUEs que son de colegios Tecnicos con 7mo
        */
        if($anio=='6to') {
            if (
                ($cue == '940007700') ||
                ($cue == '940008300')
                //($cue == '940015900') ||
                //($cue == '940015700')
            ) {
                $egresar = false;
            } else {
                $egresar = true;
            }
        } else {

            // El resto de los años, es verificado por el nivel de servicio
            switch ($nivelServicio)
            {
                case 'Común - Inicial':
                    if($anio=='Sala de 5 años') { $egresar = true; }
                    break;
                case 'Común - Secundario':
                    if($anio=='7mo') { $egresar = true; }
                    break;
            }
        }

        return $egresar;
    }

    private function slackFormat($inscripcion) {
        return $inscripcion->only(
            'id',
            'legajo_nro',
            'fecha_egreso',
            'estado_inscripcion',
            'alumno_id',
            'ciclo_id',
            'centro_id'
        );
    }
}
