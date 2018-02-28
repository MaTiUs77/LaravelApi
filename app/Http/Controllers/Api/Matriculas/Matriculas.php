<?php

namespace App\Http\Controllers\Api\Matriculas;

use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use App\Cursos;
use App\Inscripcions;
use Illuminate\Support\Facades\DB;

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