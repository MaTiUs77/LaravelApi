<?php
namespace App\Http\Controllers\Api\Inscripcion;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class Inscripcion extends Controller
{
    public $validationRules = [
        'ciclo_id' => 'required|numeric',
        'centro_id' => 'numeric',
        'curso_id' => 'numeric',
        'turno' => 'string',
        'anio' => 'string',
        'division' => 'string',
        'por_pagina' => 'string',
    ];

    public $validationMessages = [
        'required' => 'El :attribute es requerido.',
        'numeric' => 'El :attribute debe ser numerico',
        'string' => 'El :attribute debe ser solo texto',
        'accepted' => 'El :attribute debe ser: 1, on o true',
    ];

    public function lista(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules,$this->validationMessages);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $ciclo_id = Input::get('ciclo_id');
        $centro_id = Input::get('centro_id');
        $curso_id = Input::get('curso_id');
        $turno = Input::get('turno');
        $anio = Input::get('anio');
        $division = Input::get('division');
        $hermano = Input::get('hermano');
        $egresado = Input::get('egresado');
        $por_pagina = Input::get('por_pagina');

        $query = CursosInscripcions::with('Inscripcion.Hermano.Persona.Ciudad');

        if($centro_id) { $query->filtrarCentro($centro_id); }
        if($curso_id) { $query->where('curso_id',$curso_id); }
        if($ciclo_id) { $query->filtrarCiclo($ciclo_id); }
        if($turno) { $query->filtrarTurno($turno); }
        if($anio) { $query->filtrarAnio($anio); }
        if($division) { $query->filtrarDivision($division); }
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

        $countQuery= $query->count();

        if($por_pagina=='all') {
            $result = $query->paginate($countQuery);
        } else {
            if(!is_numeric($por_pagina)) {
                $por_pagina = 10;
            }
            $result = $query->paginate($por_pagina);
        }

        if(!$result)
        {
            return ['error'=>'No se encontraron resultados'];
        } else {
            if($result instanceof LengthAwarePaginator) {
                return $result->appends(Input::all());
            } else {
                $data = $result;
                return compact('data');
            }
        }
    }


    /*
     * Inscripciones 2017 Merge Inscripcion 2018
     * Filtros 6to, primaria, estatales, ciclo 2017
     *
     * Relacion
     * Persona_id
     */
    public function mergeLista(Request $request)
    {
        $por_pagina = Input::get('por_pagina');
        if(!is_numeric($por_pagina)) { $por_pagina = 10; }

        $ciclo_id = Input::get('ciclo_id');
        $anio = Input::get('anio');

        $query = CursosInscripcions::with('Inscripcion.Hermano.Persona.Ciudad');
        $query->filtrarComunPrimario();

        if($ciclo_id) { $query->filtrarCiclo($ciclo_id); }
        if($anio) { $query->filtrarAnio($anio); }

        $result =  $query->paginate($por_pagina);

        if(!$result)
        {
            return ['error'=>'No se encontraron resultados'];
        } else {
            $result->appends(Input::all());
            return $result;
        }
    }

    public function add(Request $request)
    {
        $data = $request->json();

        dd($data->get('test'));
/*
        $userId = null;
        $userCentroId = null;

        $cicloId = null;
        $cursoId = null;
        $personaId = null;
        $legajoNro = null;
        
        $inscripcion = new Inscripcion();
        $inscripcion->fecha_alta = Carbon::now()->format('Y-m-d');
        $inscripcion->usuario_id = $userId;
        $inscripcion->centro_id = $userCentroId;
        $inscripcion->ciclo_id = $cicloId;*/
        
    }
}
