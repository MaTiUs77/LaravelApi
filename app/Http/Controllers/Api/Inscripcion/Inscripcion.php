<?php
namespace App\Http\Controllers\Api\Inscripcion;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class Inscripcion extends Controller
{
    public $validationRules = [
        'centro_id' => 'required|numeric',
        'ciclo_id' => 'required|numeric',
        'turno' => 'string',
        'anio' => 'string',
        'por_pagina' => 'numeric',
        'con_hermano' => 'sometimes|accepted',
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

        $centro_id = Input::get('centro_id');
        $curso_id = Input::get('curso_id');
        $ciclo_id = Input::get('ciclo_id');
        $turno = Input::get('turno');
        $anio = Input::get('anio');
        $con_hermano = Input::get('con_hermano');
        $por_pagina = Input::get('por_pagina');

        if(!is_numeric($por_pagina)) { $por_pagina = 10; }

        $query = CursosInscripcions::with('Inscripcion.Hermano.Persona.Ciudad');

        if($centro_id) { $query->filtrarCentro($centro_id); }
        if($ciclo_id) { $query->filtrarCiclo($ciclo_id); }
        if($turno) { $query->filtrarTurno($turno); }
        if($anio) { $query->filtrarAnio($anio); }
        if($con_hermano) { $query->filtrarConHermano();}

        if($curso_id) { $query->where('curso_id',$curso_id); }

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
