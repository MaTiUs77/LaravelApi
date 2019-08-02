<?php
namespace App\Http\Controllers\Api\Inscripcion\v2;

use App\CursosInscripcions;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;
use App\Resources\InscripcionFindResource;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class InscripcionFind extends \App\Http\Controllers\Api\Inscripcion\v1\InscripcionFind
{
    // V2 Permite Multiples Cursos
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
            ->filtrarInscripcion($inscripcion_id)
            ->get();

        if($cursoInscripcions==null)
        {
            return ['error'=>'No se encontro una inscripcion con esa ID'];
        } else {
            InscripcionFindResource::withoutWrapping();
            return new InscripcionFindResource($cursoInscripcions);
        }
    }
}
