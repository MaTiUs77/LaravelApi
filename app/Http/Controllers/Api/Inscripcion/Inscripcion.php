<?php
namespace App\Http\Controllers\Api\Inscripcion;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class Inscripcion extends Controller
{
    public function info($inscripcion_id)
    {
        if(is_numeric($inscripcion_id))
        {
            $cursoInscripcions = CursosInscripcions::where('inscripcion_id',$inscripcion_id)
//                ->with('Inscripcion.Hermano.Persona.Ciudad')
                ->first();

            if(!$cursoInscripcions)
            {
                return ['error'=>'No se encontro una inscripcion con esa ID'];
            } else {

                return $cursoInscripcions;
            }
        } else
        {
            return ['error'=>'El ID es inválido'];
        }
    }

    public function lista(Request $request)
    {
        $rules = [
            'centro_id' => 'required|numeric',
            'ciclo_id' => 'required|numeric',
            'turno' => 'string',
            'anio' => 'string',
            'por_pagina' => 'numeric',
            'con_hermano' => 'sometimes|accepted',
        ];

        $messages = [
            'required' => 'El :attribute es requerido.',
            'numeric' => 'El :attribute debe ser numerico',
            'string' => 'El :attribute debe ser solo texto',
            'accepted' => 'El :attribute debe ser: 1, on o true',
        ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $centro_id = Input::get('centro_id');
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

        $result =  $query->paginate($por_pagina);

        if(!$result)
        {
            return ['error'=>'No se encontraron resultados'];
        } else {
            $result->appends(Input::all());
            return $result;
        }
    }

    public function exportExcel()
    {
        $guzzle = new Client();
        $data = $guzzle->get("http://localhost/api/inscripcion/lista",[
            'query' => Input::all()
        ]);

        $json = json_decode($data->getBody());

        $content = [];
        // Primer fila
        $content[] = ['Ciclo', 'Centro', 'Curso', 'Turno', 'DNI', 'Alumno'];
        // Contenido

        foreach($json->data as $index => $item) {
            $content[] = [
                $item->inscripcion->ciclo->nombre,
                $item->inscripcion->centro->nombre,
                $item->curso->anio,
                $item->curso->turno,
                $item->inscripcion->alumno->persona->documento_nro,

                $item->inscripcion->alumno->persona->apellidos
                .",".
                $item->inscripcion->alumno->persona->nombres
            ];
        }

        Excel::create('Inscripciones', function($excel) use($content) {
            $excel->sheet('Lista', function($sheet) use($content) {
                $sheet->fromArray($content, null, 'A1', false, false);
            });
        })->export('xls');
    }
}
