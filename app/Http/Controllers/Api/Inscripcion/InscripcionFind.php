<?php
namespace App\Http\Controllers\Api\Inscripcion;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use App\Personas;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class InscripcionFind extends Controller
{
    public function byId($inscripcion_id)
    {
        $validationRules = [
            'inscripcion_id' => 'numeric'
        ];

        $validator = Validator::make(['inscripcion_id'=>$inscripcion_id], $validationRules);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $cursoInscripcions = CursosInscripcions::where('inscripcion_id',$inscripcion_id)->first();

        if($cursoInscripcions==null)
        {
            return ['error'=>'No se encontro una inscripcion con esa ID'];
        } else {
            return $cursoInscripcions;
        }
    }

    public function byPersona($persona_id)
    {
        $validationRules = [
            'persona_id' => 'numeric',
            'ver' => 'string'
        ];

        $params = Input::all();
        $params['persona_id'] = $persona_id;

        $validator = Validator::make($params, $validationRules);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $cursoInscripcions = CursosInscripcions::filtrarPersona($persona_id)->get();

        if($cursoInscripcions==null || count($cursoInscripcions)<=0)
        {
            return ['error'=>'No se encontro una inscripcion con esa ID'];
        } else {

            switch(Input::get('ver'))
            {
                case 'primera':
                    return $cursoInscripcions->sortBy('inscripcion_id')->first();
                    break;
                case 'ultima':
                    return $cursoInscripcions->sortByDesc('inscripcion_id')->first();
                    break;
                default:
                    return $cursoInscripcions;
                    break;
            }
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

                if($cursoInscripcions==null)
                {
                    return ['error'=>'No se encontro una inscripcion con ese legajo'];
                } else {
                    return $cursoInscripcions;
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
