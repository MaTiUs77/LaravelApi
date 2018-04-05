<?php

namespace App\Http\Controllers\Api\Matriculas;

use App\Http\Controllers\Controller;
use App\Cursos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class MatriculasPorNivel extends Controller
{
    public function start(Request $request) {
        // Reglas
        $validationRules = [
            'ciclo' => 'required|numeric',
            'ciudad' => 'string',
            'ciudad_id' => 'numeric',
            'centro_id' => 'numeric',
            'nivel_servicio' => 'string'
        ];

        // Se validan los parametros
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $ciclo = Input::get('ciclo');
        $ciudad = Input::get('ciudad');
        $ciudad_id = Input::get('ciudad_id');
        $centro_id = Input::get('centro_id');
        $nivel_servicio = Input::get('nivel_servicio');

        $export = Input::get('export');

        $filtros = [];

        if(isset($ciclo)) {
            $filtros[] = "ciclo.nombre = '$ciclo'";
        }
        if(isset($ciudad)) {
            $filtros[] = "ciudad.nombre = '$ciudad'";
        }
        if(isset($ciudad_id)) {
            $filtros[] = "ciudad.id = '$ciudad_id'";
        }
        if(isset($centro_id)) {
            $filtros[] = "ins.centro_id = '$centro_id'";
        }
        if(isset($nivel_servicio)) {
            $filtros[] = "centro.nivel_servicio= '$nivel_servicio'";
        }
        $filtros = join(' AND ',$filtros);

        $inscripciones = DB::select(
            DB::raw(
                "
            select 

                ciudad.nombre as ciudad,
                centro.nivel_servicio,
                COUNT(ins.id) as matriculas
                
            FROM inscripcions ins
                
            inner join ciclos ciclo on ciclo.id = ins.ciclo_id
            inner join centros centro on centro.id = ins.centro_id
            inner join ciudads ciudad on ciudad.id = centro.ciudad_id
            inner join cursos_inscripcions cui on cui.inscripcion_id = ins.id
            inner join cursos curso on curso.id = cui.curso_id
                
            where
                
                (ins.estado_inscripcion = 'CONFIRMADA' or ins.estado_inscripcion = 'NO CONFIRMADA') AND
                $filtros

            group by 
                
                ciudad.nombre,
                centro.nivel_servicio
            ")
        );

        if(isset($export)) {
            $content = [];
            $content[] = ['Ciudad', 'Nivel de servicio', 'Matriculas'];
            // Contenido
            foreach($inscripciones as $item) {
                $content[] = [
                    $item->ciudad,
                    $item->nivel_servicio,
                    $item->matriculas,
                ];
            }

            MatriculasExport::toExcel("Matriculas cuantitativa $ciclo","Por nivel $nivel_servicio",$content);
        }

        return $inscripciones;
    }
}