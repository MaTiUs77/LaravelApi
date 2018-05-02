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
        'ciclo_id' => 'required_without:ciclo|numeric',
        'ciclo' => 'required_without:ciclo_id|numeric',
        'centro_id' => 'numeric',
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

        $ciclo_id = Input::get('ciclo_id');
        $ciclo = Input::get('ciclo');

        $centro_id = Input::get('centro_id');
        $curso_id = Input::get('curso_id');
        $turno = Input::get('turno');
        $anio = Input::get('anio');
        $division = Input::get('division');
        $hermano = Input::get('hermano');
        $egresado = Input::get('egresado');
        $estado_inscripcion = Input::get('estado_inscripcion');

        $por_pagina = Input::get('por_pagina');

        $query = CursosInscripcions::with('Inscripcion.Hermano.Persona.Ciudad');

        if($centro_id) { $query->filtrarCentro($centro_id); }
        if($curso_id) { $query->where('curso_id',$curso_id); }
        if($ciclo_id) { $query->filtrarCiclo($ciclo_id); }
        if($ciclo) { $query->filtrarCicloNombre($ciclo); }
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

        if($por_pagina=='all') {
            $countQuery= $query->count();
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
}
