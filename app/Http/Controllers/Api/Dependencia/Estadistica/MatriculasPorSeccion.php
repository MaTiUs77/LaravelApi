<?php

namespace App\Http\Controllers\Api\Dependencia\Estadistica;

use App\Http\Controllers\Api\Utilities\Export;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class MatriculasPorSeccion extends Controller
{
    /*
     * CICLO | CUE | ESTABLECIMIENTO | SECTOR | NIVEL DE SERVICIO | AÑO | DIVISIÓN | TURNO | TIPO | MATRÍCULA | VARONES | MUJERES
     */
    public function start(Request $request) {
        // Reglas de validacion
        $validationRules = [
            'ciclo' => 'required|numeric',
            'ciudad' => 'string',
            'ciudad_id' => 'numeric',
            'centro_id' => 'numeric',
            'curso_id' => 'numeric',
            'anio' => 'string',
            'nivel_servicio' => 'string',
            'division' => 'string',
            'cue' => 'numeric'
        ];

        // Se validan los parametros
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        // Generacion de query
        $query = Inscripcions::select([
            DB::raw('
            
            inscripcions.centro_id,
            cursos.id as curso_id,
            ciudads.id as ciudad_id,

            ciudads.nombre as ciudad,

            centros.cue,
            centros.nombre,
            centros.nivel_servicio,
            centros.sector,
            
            cursos.anio,
            cursos.division,
            cursos.turno,
            cursos.tipo,
            
            cursos.plazas,
            COUNT(inscripcions.id) as matriculas,
            (
              cursos.plazas - COUNT(inscripcions.id)
            ) as vacantes
            ')
        ])
            ->join('cursos_inscripcions','cursos_inscripcions.inscripcion_id','inscripcions.id')
            ->join('ciclos','inscripcions.ciclo_id','ciclos.id')
            ->join('centros','inscripcions.centro_id','centros.id')
            ->join('cursos','cursos_inscripcions.curso_id','cursos.id')
            ->join('ciudads','centros.ciudad_id','ciudads.id')

            ->where('inscripcions.estado_inscripcion','CONFIRMADA');

        $query = $this->aplicarFiltros($query);

        // Agrupamiento y ejecucion de query
        $inscripciones = $query->groupBy([
            'inscripcions.centro_id',
            'cursos.id',
            'cursos.anio',
            'cursos.division',
            'cursos.turno',
            'cursos.plazas'
        ])->get();

        // Exportacion a Excel si es solicitado
        $this->exportar($inscripciones);

        return $inscripciones;
    }

    private function exportar($resultado) {
        $export = Input::get('export');
        $ciclo = Input::get('ciclo');

        if(isset($export)) {
            $content = [];
            $content[] = ['Ciudad', 'Establecimiento', 'Nivel de Servicio', 'Año', 'Division', 'Turno', 'Plazas', 'Matriculas','Vacantes'];
            // Contenido
            foreach($resultado as $item) {
                $content[] = [
                    $item->ciudad,
                    $item->nombre,
                    $item->nivel_servicio,
                    $item->anio,
                    $item->division,
                    $item->turno,
                    $item->plazas,
                    $item->matriculas,
                    $item->vacantes
                ];
            }

            Export::toExcel("Matriculas cuantitativa $ciclo","Matriculas",$content);
        }
    }

    private function aplicarFiltros($query) {
        // Obtencion de parametros
        $ciclo = Input::get('ciclo');
        $ciudad = Input::get('ciudad');
        $ciudad_id = Input::get('ciudad_id');
        $centro_id = Input::get('centro_id');
        $curso_id = Input::get('curso_id');
        $anio = Input::get('anio');
        $nivel_servicio = Input::get('nivel_servicio');
        $division = Input::get('division');
        $cue = Input::get('cue');

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
        if(isset($cue)) {
            $query = $query->where('centros.cue',$cue);
        }
        if(isset($centro_id)) {
            $query = $query->where('inscripcions.centro_id',$centro_id);
        }
        if(isset($curso_id)) {
            $query = $query->where('cursos.id',$curso_id);
        }
        if(isset($nivel_servicio)) {
            $query = $query->where('centros.nivel_servicio',$nivel_servicio);
        }
        if(isset($anio)) {
            $query = $query->where('cursos.anio',$anio);
        }
        if(isset($division)) {
            $query = $query->where('cursos.division',$division);
        }

        return $query;
    }
}