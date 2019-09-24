<?php
namespace App\Http\Controllers\Api\Inscripcion\v1;

use App\CursosInscripcions;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class InscripcionFind extends Controller
{
    public function startFind() {
        // Resultados unicos
        $inscripcion_id = Input::get('id');
        $persona_id= Input::get('persona_id');
        $legajo_nro= Input::get('legajo_nro');
        $documento_nro= Input::get('documento_nro');

        // Multiples resultados
        $fullname= Input::get('fullname');

        if($inscripcion_id){
            return $this->byId($inscripcion_id);
        }

        if($persona_id){
            return $this->byPersona($persona_id);
        }

        if($legajo_nro){
            return $this->byLegajo($legajo_nro);
        }

        if($documento_nro){
            return $this->byDocumentoNro($documento_nro);
        }

        if($fullname){
            return $this->byPersonaFullname();
        }

        return ['error'=> 'No definio ningun filtro'];
    }

    // INSCRIPCIONES
    public function byId($inscripcion_id)
    {
        $input = ['inscripcion_id'=>$inscripcion_id];
        $rules = ['inscripcion_id' => 'numeric'];
        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        $cursoInscripcions = CursosInscripcions::withOnDemand([
            'curso',
            'inscripcion.ciclo',
            'inscripcion.centro.ciudad',
            'inscripcion.alumno.persona.ciudad',
        ])
            ->where('inscripcion_id',$inscripcion_id)
            ->first();

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
        $input = [
            'personas_id'=>$persona_id,
        ];
        $rules = [
            'persona_id' => 'numeric',
            'ver' => 'string',
        ];
        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        $query= CursosInscripcions::withOnDemand([
            'curso',
            'inscripcion.ciclo',
            'inscripcion.centro.ciudad',
            'inscripcion.alumno.persona.ciudad',
        ]);

        $cursoInscripcions = $query->filtrarPersona($persona_id)->get();

        if($cursoInscripcions==null || count($cursoInscripcions)<=0)
        {
            return ['error'=>'No se encontro una inscripcion con esa ID'];
        } else {
            switch(request('ver'))
            {
                case 'primera':
                    $result = $cursoInscripcions->sortBy('inscripcion.legajo_nro')->first();
                    break;
                case 'ultima':
                    $result = $cursoInscripcions->sortByDesc('inscripcion.legajo_nro')->first();
                break;
                default:
                    $result = $cursoInscripcions;
                break;
            }

            return $result;
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

            if($cursoInscripcions==null || count($cursoInscripcions)<=0)
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
