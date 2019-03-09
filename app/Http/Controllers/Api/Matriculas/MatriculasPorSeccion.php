<?php

namespace App\Http\Controllers\Api\Matriculas;

use App\Http\Controllers\Api\Utilities\Export;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class MatriculasPorSeccion extends Controller
{
    public function start(Request $request) {
        $nivel_servicio_rule = is_array(Input::get('nivel_servicio')) ? 'array' : 'string';
        $estado_inscripcion_rule = is_array(Input::get('estado_inscripcion')) ? 'array' : 'string';

        // Reglas de validacion
        $validationRules = [
            'ciclo' => 'required|numeric',
            'ciudad' => 'string',
            'ciudad_id' => 'numeric',
            'centro_id' => 'numeric',
            'nivel_servicio' => $nivel_servicio_rule,
            'estado_inscripcion' => $estado_inscripcion_rule,
            'anio' => 'string',
            'division' => 'string',
            'sector' => 'string',
            'status' => 'string'
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
            ) as vacantes,
            COUNT(personas.sexo) as varones
            ')
        ])
            /*
             * REQUIERE OPTIMIZAR
             *
            (
                select count(ins.id) as hermanos
                from  cursos_inscripcions as curi
                left join inscripcions as ins on ins.id = curi.inscripcion_id
                where
                   ins.tipo_inscripcion = "Hermano de alumno regular"
                and ins.centro_id = centros.id
                and curi.curso_id = cursos.id
            ) as por_hermano

             */

            ->join('cursos_inscripcions','cursos_inscripcions.inscripcion_id','inscripcions.id')
            ->join('ciclos','inscripcions.ciclo_id','ciclos.id')
            ->join('centros','inscripcions.centro_id','centros.id')
            ->join('cursos', function($join){
                $join->on('cursos_inscripcions.curso_id','cursos.id')
                    ->where('division','<>','');
            })
            ->join('ciudads','centros.ciudad_id','ciudads.id')

            ->leftJoin('alumnos','inscripcions.alumno_id','alumnos.id')
            ->leftJoin('personas', function ($join) {
                $join->on('alumnos.persona_id', '=', 'personas.id')
                    ->where('personas.sexo', '=', 'MASCULINO');
            });

        $query = $this->aplicarFiltros($query);
        $query = $this->aplicarOrden($query);

        // Agrupamiento y ejecucion de query
        $query = $query->groupBy([
            'inscripcions.centro_id',
            'cursos.id',
            'cursos.anio',
            'cursos.division',
            'cursos.turno',
            'cursos.plazas'
        ]);

        $por_pagina = Input::get('por_pagina');

        $result = $query->customPagination($por_pagina);

        if(Input::get('ciclo')==2019)
        {
            foreach($result->items() as $transform)
            {
                switch ($transform->nivel_servicio)
                {
                    case 'Común - Inicial':
                    case 'Común - Primario':
                        $transform->plazas = 21;
                        $transform->vacantes= $transform->plazas - $transform->matriculas;
                        break;
                }
            }
        }

        $this->exportar($result);

        return $result;
    }

    private function exportar($paginationResult) {
        $ciclo = Input::get('ciclo');

        // Exportacion a Excel
        if(Input::get('export')) {
            $content = [];
            $content[] = ['Ciudad', 'Establecimiento', 'Nivel de Servicio', 'Año', 'Division', 'Turno', 'Plazas', 'Matriculas','Vacantes'];
            // Contenido

            foreach($paginationResult as $item) {
                $content[] = [
                    $item->ciudad,
                    $item->nombre,
                    $item->nivel_servicio,
                    $item->anio,
                    $item->division,
                    $item->turno,
                    $item->plazas,
                    $item->matriculas,
                    $item->vacantes,
                    $item->varones
                ];
            }

            Export::toExcel("Matricula Cuantitativa Por Seccion - Ciclo $ciclo","Matriculas por Seccion",$content);
        }
    }

    private function aplicarFiltros($query) {
        // Obtencion de parametros
        $ciclo = Input::get('ciclo');
        $ciudad = Input::get('ciudad');
        $ciudad_id = Input::get('ciudad_id');
        $centro_id = Input::get('centro_id');
        $anio = Input::get('anio');
        $division = Input::get('division');
        $nivel_servicio = Input::get('nivel_servicio');
        $sector= Input::get('sector');
        $estado_inscripcion= Input::get('estado_inscripcion');
        $status= Input::get('status');

        // Por defecto Curso.status = 1
        if(isset($status)) {
            if(is_numeric($status)) {
                $query = $query->where('cursos.status',$status);
            }
        } else {
            $query = $query->where('cursos.status',1);
        }

        // Por defecto se listan las inscripciones confirmadas
        if(isset($estado_inscripcion)) {
            if(is_array($estado_inscripcion))
            {
                $query = $query->where(function($subquery)
                {
                    foreach(Input::get('estado_inscripcion') as $select) {
                        $subquery->orWhere('inscripcions.estado_inscripcion',$select);
                    }
                });
            } else
            {
                $query = $query->where('inscripcions.estado_inscripcion','CONFIRMADA');
            }
        }

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
        if(isset($curso_id)) {
            $query = $query->where('cursos.id',$curso_id);
        }
        if(isset($sector)) {
            $query = $query->where('centros.sector',$sector);
        }
        if(isset($nivel_servicio)) {
            if(is_array($nivel_servicio))
            {
                $query = $query->where(function($subquery)
                {
                    foreach(Input::get('nivel_servicio') as $select) {
                        $subquery->orWhere('centros.nivel_servicio', $select);
                    }
                });
            } else
            {
                $query = $query->where('centros.nivel_servicio',$nivel_servicio);
            }
        }
        if(isset($anio)) {
            $query = $query->where('cursos.anio',$anio);
        }
        if(isset($division)) {
            if($division=='vacia' || $division=='sin' || $division == null) {
                $query = $query->where('cursos.division','');
            } else if($division=='con'){
                $query = $query->where('cursos.division','<>','');
            } else {
                $query = $query->where('cursos.division',$division);
            }
        }

        return $query;
    }

    private function aplicarOrden($query) {
        $orderBy = Input::get('order');
        $orderDir = Input::get('order_dir');
        if($orderBy)
        {
            $query = $query->orderBy($orderBy,$orderDir);
        }
        return $query;
    }
}