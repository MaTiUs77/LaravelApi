<?php
namespace App\Http\Controllers\Api\Inscripcion;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class InscripcionFind extends Controller
{
    public function startFind() {
        $inscripcion_id = Input::get('inscripcion_id');
        $persona_id= Input::get('persona_id');
        $legajo_nro= Input::get('legajo_nro');
        $fullname= Input::get('fullname');
        $documento_nro= Input::get('documento_nro');

        if($inscripcion_id){
            return $this->byId($inscripcion_id);
        }

        if($persona_id){
            return $this->byPersona($persona_id);
        }

        if($fullname){
            return $this->byPersonaFullname();
        }

        if($legajo_nro){
            return $this->byLegajo($legajo_nro);
        }

        if($documento_nro){
            return $this->byDocumentoNro($documento_nro);
        }

        return ['error'=> 'No definio ningun filtro'];
    }

    // INSCRIPCIONES
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

    // PERSONAS
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
            $eagers = [
                'curso',
                'inscripcion.ciclo',
                'inscripcion.centro.ciudad',
                'inscripcion.alumno.persona.ciudad'
            ];

            switch(Input::get('ver'))
            {
                case 'primera':
                    // Luego de usar sortBy || sortByDesc es necesario recargar los eager loaders
                    $sorted = $cursoInscripcions->sortBy('inscripcion.legajo_nro')->first();
                    $sorted->load($eagers);
                    return $sorted;
                    break;
                case 'ultima':
                    //return $cursoInscripcions->sortByDesc('inscripcion.legajo_nro')->first(); <--- BUG, pierde la relacion de los eager loaders

                    // Luego de usar sortBy || sortByDesc es necesario recargar los eager loaders
                    $sorted = $cursoInscripcions->sortByDesc('inscripcion.legajo_nro')->first();
                    $sorted->load($eagers);
                    return $sorted;
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

        $cursoInscripcions = CursosInscripcions::filtrarPersonaFullname($fullname)->paginate();

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
    public function byDocumentoNro($documento_nro)
    {
        if(is_numeric($documento_nro))
        {

            $cursoInscripcions = CursosInscripcions::filtrarPersonaDocumentoNro($documento_nro)->get();
            if($cursoInscripcions==null)
            {
                return ['error'=>'No se encontro una inscripcion con ese numero de documento'];
            } else {
                return $cursoInscripcions;
            }
        } else
        {
            return ['error'=>'El documento es inválido'];
        }
    }
}
