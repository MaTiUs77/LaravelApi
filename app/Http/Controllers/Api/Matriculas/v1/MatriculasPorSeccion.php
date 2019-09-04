<?php

namespace App\Http\Controllers\Api\Matriculas\v1;

use App\Cursos;
use App\CursosInscripcions;
use App\Http\Controllers\Api\Utilities\Export;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use App\Titulacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
            
            inscripcions.ciclo_id as ciclo_id,
            inscripcions.centro_id,
            cursos.id as curso_id,
            cursos.titulacion_id,
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
            cursos.pareja_pedagogica,
            cursos.maestra_apoyo_inclusion,

            cursos.plazas,
            COUNT(inscripcions.id) as matriculas,
            (
              cursos.plazas - COUNT(inscripcions.id)
            ) as vacantes,
            COUNT(personas.sexo) as varones,
            COUNT(inscripcions.hermano_id) as por_hermano,
            
            CAST(SUM(if(inscripcions.estado_inscripcion  = "CONFIRMADA", 1, 0)) AS UNSIGNED) AS confirmadas
            ')
        ])
            ->join('cursos_inscripcions','cursos_inscripcions.inscripcion_id','inscripcions.id')
            ->join('ciclos','inscripcions.ciclo_id','ciclos.id')
            ->join('centros','inscripcions.centro_id','centros.id')
            ->join('cursos','cursos_inscripcions.curso_id','cursos.id')
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
            'inscripcions.ciclo_id',
            'inscripcions.centro_id',
            'cursos.id',
            'cursos.anio',
            'cursos.division',
            'cursos.turno',
            'cursos.titulacion_id',
            'cursos.plazas'
        ]);

        if(request('por_pagina')=='all') {
            $result = $query->get();
            $items = $result;
        } else {
            $result = $query->customPagination();
            $items = $result->items();
        }

        foreach($items as $item) {
            // Se carga la relacion con el modelo Titulacion
            $item->titulacion = Titulacion::select('nombre','nombre_abreviado')->find($item->titulacion_id);
/*            $item->confirmadas = CursosInscripcions::filtrarCiclo($item->ciclo_id)
                ->filtrarCurso($item->curso_id)
                ->filtrarEstadoInscripcion('CONFIRMADA')
                ->count();*/

            $item->confirmadas_excede_plaza = ($item->confirmadas > $item->plazas);

            /*// Modifica las plazas y vacantes del ciclo 2019
            if(Input::get('ciclo')==2019)
            {
                switch ($item->nivel_servicio)
                {
                    case 'Común - Inicial':
                    case 'Común - Primario':
                        $item->plazas = 24;
                        // Harcodeada
                        if($item->anio=='Sala de 3 años') {
                            $item->plazas = 20;
                        }
                        $item->vacantes= $item->plazas - $item->matriculas;
                        break;
                }
            }*/
        }

        $this->exportar($result);

        return $result;
    }

    private function exportar($paginationResult) {
        $ciclo = Input::get('ciclo');

        // Exportacion a Excel
        if(Input::get('export')) {
            $content = [];
            $content[] = ['Ciudad', 'Establecimiento', 'Nivel de Servicio', 'Año', 'Division', 'Turno', 'Plazas', 'Matriculas','Vacantes','Varones','Por Hermanos'];
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
                    $item->varones,
                    $item->por_hermano
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
        $curso_id = Input::get('curso_id');
        $anio = Input::get('anio');
        $division = Input::get('division');
        $nivel_servicio = Input::get('nivel_servicio');
        $sector= Input::get('sector');
        $estado_inscripcion= Input::get('estado_inscripcion');
        $status= Input::get('status');
        $hermano= Input::get('hermano');

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
        if(isset($hermano)) {
            $query = $query->where('inscripcions.hermano_id','<>',null);
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
        $orderBy = [
            'centros.nombre' => 'asc',
            'cursos.anio' => 'asc',
            'cursos.division' => 'asc'
        ];

        if($orderBy) {
            foreach ($orderBy as $order => $dir) {
                $query = $query->orderBy($order,$dir);
            }
        }

        return $query;
    }
}