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
            /*
             * Este fix permite ordenar los resultados por legajo_nro
             * Eso es debido a que al ordenar un collection con $collection->sortBy()
             * por algun motivo se pierde la relacion con los eager loaders
             *
             * Hay que informar este bug en los issue de laravel
             */
            $byLegajos = $cursoInscripcions->mapWithKeys(function ($item,$index) {
                $obj = [
                    'legajo_nro' => $item->inscripcion->legajo_nro,
                    'index' => $index
                ];
                return [$item->inscripcion->legajo_nro => $obj];
            });
            $ordered = $byLegajos->sortBy('legajo_nro');
            //// FIN DEL FIX ////

            switch(Input::get('ver'))
            {
                case 'primera':
                    $index = $ordered->first()['index'];
                    return $cursoInscripcions->get($index);
                    break;
                case 'ultima':
                    //return $cursoInscripcions->sortByDesc('inscripcion.legajo_nro')->first(); <--- BUG, pierde la relacion de los eager loaders
                    $index = $ordered->last()['index'];
                    return $cursoInscripcions->get($index);
                    break;
                default:
                    return $cursoInscripcions;
                    break;
            }
        }
    }

    public function byPersonaFullname()
    {
        $fullname = Input::get('fullname');
        $validationRules = [
            'fullname' => 'string',
            'ver' => 'string'
        ];

        $params = Input::all();
        $params['fullname'] = $fullname;

        $validator = Validator::make($params, $validationRules);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $cursoInscripcions = CursosInscripcions::filtrarPersonaFullname($fullname)->get();

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
            $cursoInscripcions = CursosInscripcions::filtrarLegajo($legajo_nro)->first();

            if($cursoInscripcions==null)
            {
                return ['error'=>'No se encontro una inscripcion con ese legajo'];
            } else {
                return $cursoInscripcions;
            }
        } else
        {
            return ['error'=>'El legajo es inválido'];
        }
    }
}
