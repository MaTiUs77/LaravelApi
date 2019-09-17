<?php

namespace App\Http\Controllers\Api\Matriculas\v1;

use App\Http\Controllers\Api\Utilities\Export;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class MatriculasPorNivel extends Controller
{
    public function start(Request $request) {
        // Reglas de validacion
        $validationRules = [
            'ciclo' => 'required|numeric',
            'ciudad' => 'string',
            'ciudad_id' => 'numeric',
            'centro_id' => 'numeric',
            'nivel_servicio' => 'string',
            'estado_inscripcion' => 'string',
        ];

        // Se validan los parametros
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        // Generacion de query
        $query = Inscripcions::select([
            'ciudads.nombre as ciudad',
            'centros.nivel_servicio',
            DB::raw('COUNT(inscripcions.id) as matriculas')
        ])
            ->join('cursos_inscripcions','cursos_inscripcions.inscripcion_id','inscripcions.id')
            ->join('ciclos','inscripcions.ciclo_id','ciclos.id')
            ->join('centros','inscripcions.centro_id','centros.id')
            ->join('ciudads','centros.ciudad_id','ciudads.id');

            //->where('inscripcions.estado_inscripcion','CONFIRMADA');

        $query = $this->aplicarFiltros($query);

        // Agrupamiento y ejecucion de query
        $inscripciones = $query->groupBy([
            'ciudads.nombre',
            'centros.nivel_servicio'
        ])->get();

        // Exportacion a excel
        $this->exportar($inscripciones);

        return $inscripciones;
    }

    private function exportar($lista) {
        $ciclo = Input::get('ciclo');

        // Exportacion a Excel
        if(Input::get('export')) {
            $content = [];
            $content[] = ['Ciudad', 'Nivel de servicio', 'Matriculas'];
            // Contenido
            foreach($lista as $item) {
                $content[] = [
                    $item->ciudad,
                    $item->nivel_servicio,
                    $item->matriculas,
                ];
            }

            Export::toExcel("Matricula Cuantitativa Por Nivel - Ciclo $ciclo","Matriculas por Nivel",$content);
        }
    }

    private function aplicarFiltros($query) {
        // Obtencion de parametros
        $ciclo = Input::get('ciclo');
        $ciudad = Input::get('ciudad');
        $ciudad_id = Input::get('ciudad_id');
        $centro_id = Input::get('centro_id');
        $nivel_servicio = Input::get('nivel_servicio');
        $estado_inscripcion = Input::get('estado_inscripcion');

        // Aplicacion de filtros
        if(isset($ciclo)) {
            $query = $query->where('ciclos.nombre',$ciclo);
        }
        if(isset($ciudad)) {
            $query = $query->where('ciudads.nombre',$ciudad);
        }
        if(isset($ciudad_id)) {
            $query = $query->where('ciudads.id',$ciudad_id);
        }
        if(isset($centro_id)) {
            $query = $query->where('inscripcions.centro_id',$centro_id);
        }
        if(isset($nivel_servicio)) {
            $query = $query->where('centros.nivel_servicio',$nivel_servicio);
        }
        if(isset($estado_inscripcion)) {
            $query = $query->where('inscripcions.estado_inscripcion',$estado_inscripcion);
        } else {
            $query = $query->where('inscripcions.estado_inscripcion','CONFIRMADA');
        }

        return $query;
    }
}