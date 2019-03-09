<?php
namespace App\Http\Controllers\Api\Inscripcion;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class Inscripcion extends Controller
{
    public $validationRules = [
        'ciclo_id' => 'required_without_all:ciclo,alumno_id,documento_nro|numeric',
        'ciclo' => 'required_without_all:ciclo_id,alumno_id,documento_nro|numeric',
        'alumno_id' => 'required_without_all:ciclo,ciclo_id,documento_nro|numeric',
        'documento_nro' => 'required_without_all:ciclo,ciclo_id,alumno_id|numeric',
        'persona' => 'string',
        'centro_id' => 'numeric',
        'ciudad' => 'string',
        'nivel_servicio' => 'string',
        'curso_id' => 'numeric',
        'turno' => 'string',
        'anio' => 'string',
        'division' => 'string',
        'estado_inscripcion' => 'string',
        'por_pagina' => 'string',
    ];

    public function lista(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        // Minimo requerido
        $ciclo_id = Input::get('ciclo_id');
        $ciclo = Input::get('ciclo');
        $alumno_id= Input::get('alumno_id');
        $documento_nro= Input::get('documento_nro');

        // Centros
        $ciudad = Input::get('ciudad');
        $centro_id = Input::get('centro_id');
        $sector= Input::get('sector');
        $nivel_servicio = Input::get('nivel_servicio');
        $curso_id = Input::get('curso_id');
        $turno = Input::get('turno');
        $anio = Input::get('anio');
        $division = Input::get('division');

        // Personas
        $persona= Input::get('persona');

        $hermano = Input::get('hermano');
        $egresado = Input::get('egresado');
        $estado_inscripcion = Input::get('estado_inscripcion');

        $por_pagina = Input::get('por_pagina');

        $query = CursosInscripcions::with('Inscripcion.Hermano.Persona.Ciudad');

        if($ciclo_id) { $query->filtrarCiclo($ciclo_id); }
        if($ciclo) { $query->filtrarCicloNombre($ciclo); }
        if($alumno_id) { $query->filtrarAlumnoId($alumno_id); }
        if($documento_nro) { $query->filtrarPersonaDocumentoNro($documento_nro); }

        if($persona) { $query->filtrarPersonaFullname($persona); }

        if($sector) { $query->filtrarSector($sector); }
        if($centro_id) { $query->filtrarCentro($centro_id); }
        if($ciudad) { $query->filtrarCiudad($ciudad); }
        if($nivel_servicio) { $query->filtrarNivelServicio($nivel_servicio); }

        if($curso_id) { $query->filtrarCurso($curso_id); }
        if($turno) { $query->filtrarTurno($turno); }
        if($anio) { $query->filtrarAnio($anio); }
        if($division) { $query->filtrarDivision($division); }
        if($estado_inscripcion) { $query->filtrarEstadoInscripcion($estado_inscripcion);}
        if($hermano) {
            switch($hermano){
                case "si":
                    $query->filtrarConHermano();
                    break;
                case "no":
                    $query->filtrarSinHermano();
                    break;
            }
        }
        if($egresado) {
            switch($egresado){
                case "si":
                    $query->filtrarConEgreso();
                    break;
                case "no":
                    $query->filtrarSinEgreso();
                    break;
            }
        }

        return $query->customPagination($por_pagina);
    }
}
