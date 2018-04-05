<?php

namespace App\Http\Controllers\Api\Matriculas;

use App\Http\Controllers\Controller;
use App\Cursos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class Matriculas extends Controller
{
    /*
     * Este metodo se encarga de listar todas las inscripciones realizadas, y las agrupa segun el siguiente filtro
     *
     * Inscripcion en -> ciclo_id, centro_id, curso.anio, curso.division, curso.turno
     *
     * El filtro obtiene la cantidad de matriculas y sus plazas, lo que permite obtener las vacantes.
     *
     * Esta consulta a su vez actualiza los datos en la tabla Cursos segun el Curso.id
     *
     */
    public function recuento($cicloNombre)
    {
        if(!is_numeric($cicloNombre))
        {
            $error = 'El ciclo debe ser numerico, ej: 2018';
            return compact('error');
        } else {

            $reset = $this->resetMatriculaYPlazas();

            $inscripciones = DB::select(
                DB::raw(
                    "
            select 
                
                ins.ciclo_id,
                ins.centro_id,            
                curso.id,
                curso.anio,
                curso.division,
                curso.turno,
                curso.plazas,
                COUNT(ins.id) as matriculaCount,
                (
                  curso.plazas - COUNT(ins.id)
                ) as vacantesCount
                
                FROM inscripcions ins
                
                inner join ciclos ci on ci.id = ins.ciclo_id
                inner join centros ce on ce.id = ins.centro_id
                inner join cursos_inscripcions cui on cui.inscripcion_id = ins.id
                inner join cursos curso on curso.id = cui.curso_id
                
                where
                
                ci.nombre = $cicloNombre AND 
               (ins.estado_inscripcion = 'CONFIRMADA' or ins.estado_inscripcion = 'NO CONFIRMADA')
--              ins.centro_id = 7 
--              curso.division <> ''

                group by 
    
                ins.ciclo_id,            
                ins.centro_id,            
                curso.id,
                curso.anio,
                curso.division,
                curso.turno,
                curso.plazas")
            );

            foreach($inscripciones as $item)
            {
                $curso = Cursos::find($item->id);
                $curso->matricula = $item->matriculaCount;
                $curso->vacantes = $item->vacantesCount;
                $curso->save();
            }

            return compact('reset','inscripciones');
        }
    }
    
    public function cuantitativa(Request $request) {
        // Reglas
        $validationRules = [
            'ciclo' => 'required|numeric',
            'ciudad' => 'string',
            'ciudad_id' => 'numeric',
            'centro_id' => 'numeric',
            'anio' => 'string',
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
        $anio = Input::get('anio');

        $filtros = [];

        if(isset($ciclo)) {
            $filtros[] = "ciclo.nombre = '$ciclo'";
        }
        if(isset($anio)) {
            $filtros[] = "curso.anio = '$anio'";
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

        $filtros = join(' AND ',$filtros);

        $inscripciones = DB::select(
            DB::raw(
                "
            select 
                
                ins.centro_id,
                curso.id as curso_id,
                ciudad.id as ciudad_id,

                ciudad.nombre as ciudad,

                centro.nombre,
                
                curso.anio,
                curso.division,
                curso.turno,
                curso.plazas,
                COUNT(ins.id) as matriculas,
                (
                  curso.plazas - COUNT(ins.id)
                ) as vacantes
                
                FROM inscripcions ins
                
                inner join ciclos ciclo on ciclo.id = ins.ciclo_id
                inner join centros centro on centro.id = ins.centro_id
                inner join ciudads ciudad on ciudad.id = centro.ciudad_id
                inner join cursos_inscripcions cui on cui.inscripcion_id = ins.id
                inner join cursos curso on curso.id = cui.curso_id
                
                where
                
                (ins.estado_inscripcion = 'CONFIRMADA' or ins.estado_inscripcion = 'NO CONFIRMADA') AND
                $filtros
--              curso.division <> ''

                group by 
    
                ins.centro_id,            
                curso.id,
                curso.anio,
                curso.division,
                curso.turno,
                curso.plazas")
        );

        return $inscripciones;
    }

    public function cuantitativaPorNivel(Request $request) {
        // Reglas
        $validationRules = [
            'ciclo' => 'required|numeric',
            'ciudad' => 'string',
            'ciudad_id' => 'numeric',
            'centro_id' => 'numeric',
            'nivel_servicio' => 'string',
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

                ins.centro_id,
                ciudad.id as ciudad_id,
                ciudad.nombre as ciudad,
                centro.nombre,
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
                
                ins.centro_id, 
                ciudad.nombre,
                centro.nivel_servicio
            ")
        );

        return $inscripciones;
    }

    // Actualiza las vacantes con el valor de plazas, y las matriculas las deja en cero
    public function resetMatriculaYPlazas()
    {
        $result = DB::table('cursos')->update([
            'matricula' => 0,
            'vacantes' => DB::raw('plazas')
        ]);

        return $result;
    }
}