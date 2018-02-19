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
    public function recuento()
    {
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
                COUNT(ins.id) as matriculados,
                (
                  curso.plazas - COUNT(ins.id)
                ) as vacantes
                
                FROM inscripcions ins
                
                inner join ciclos ci on ci.id = ins.ciclo_id
                inner join centros ce on ce.id = ins.centro_id
                inner join cursos_inscripcions cui on cui.inscripcion_id = ins.id
                inner join cursos curso on curso.id = cui.curso_id
                
                where
                
                -- ci.nombre = 2017
                -- i.ciclo_id = 4
                -- and
                ins.centro_id = 7 
                AND 
                curso.division <> ''
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
            $curso->matricula = $item->matriculados;
            $curso->vacantes = $item->vacantes;
            $curso->save();
        }

        return compact('reset','inscripciones');
    }

    private function resetMatriculaYPlazas()
    {
        // Antes que nada se devuelven a cero todas las matriculas
        // las vacantes pasan a ser el total de plazas
        $result = DB::table('cursos')->update([
            'matricula' => 0,
            'vacantes' => DB::raw('plazas')
        ]);

        return $result;
    }
}