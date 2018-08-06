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
    public function __construct(Request $req)
    {
        //$this->middleware('jwt',['except'=>['index']]);
    }

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

    /*
     * 21 Plazas se aplica
     * Inicial y primaria tomar cursos sin division y con turno otro
     *
     */
    public function recuentoVacantes__($cicloNombre)
    {
        if(!is_numeric($cicloNombre))
        {
            $error = 'El ciclo debe ser numerico, ej: 2019';
            return compact('error');
        } else {
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
               (ins.estado_inscripcion = 'CONFIRMADA' or ins.estado_inscripcion = 'NO CONFIRMADA') and
                ins.centro_id = 62 and 
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
                $curso->matricula = $item->matriculaCount;
                $curso->vacantes = $item->vacantesCount;
                $curso->save();
            }

            return compact('reset','inscripciones');
        }
    }

    public function recuentoVacantes($cicloNombre)
    {
        $cursos = Cursos::with('Centro')
            ->where('division', '')
            ->where('turno', '<>', 'Otro')
            //->where('turno', 'tarde')
            //->where('centro_id', '80')
            //->where('anio', 'Sala de 4 años')
            ->get();

        $output = [];
        foreach ($cursos as $curso) {

            // Unidades en plazas, matriculas y vacantes de cursos sin division del centro
            $recuento = $this->cuantificarMatriculasSinDivision(
                $curso->centro_id,
                $curso->anio,
                $curso->turno
            );

            if($recuento)
            {
                $curso = Cursos::find($curso->id);
                $curso->plazas = $recuento->plazas;
                $curso->matricula = $recuento->matricula;
                $curso->vacantes = $recuento->plazas - $recuento->vacantes;
                $curso->save();
            }

            $output[] = [
                'curso' => $curso,
                'recuento' => $recuento
            ];
        }

        return $output;
    }

    private function cuantificarMatriculasSinDivision($centro_id,$anio,$turno)
    {
        $query = Cursos::select(
            DB::raw("       
                turno,
                anio,
                SUM(plazas) as plazas,
                SUM(matricula) as matricula,
                SUM(vacantes) as vacantes
            "))
            ->where('centro_id',$centro_id)
            ->where('division','<>','')
            ->where('anio',$anio)
            ->where('turno',$turno)
            ->groupBy('anio')
            ->groupBy('turno')
            ->first();
        ;

        return $query;
    }
}