<?php

namespace App\Http\Controllers\Api\Matriculas;

use App\CursosInscripcions;
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
                ins.estado_inscripcion = 'CONFIRMADA'

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

    public function recuentoVacantes(Request $request)
    {
        $validationRules = [
            'ciclo_id' => 'required_without_all:ciclo|numeric',
            'ciclo' => 'required_without_all:ciclo_id|numeric',
            'centro_id' => 'numeric',
            'anio' => 'string',
        ];

        // Validacion de datos
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        // Minimo requerido
        $ciclo_id = Input::get('ciclo_id');
        $ciclo = Input::get('ciclo');

        // Centros
        $centro_id = Input::get('centro_id');
        $nivel_servicio = Input::get('nivel_servicio');
        $anio = Input::get('anio');

        // Ejecuta una normalizacion de las plazas, matriculas y vacantes del ciclo solicitado en sus secciones sin division
        $normalizacion = $this->normalizacionDeCursosSinDivision();

        // Inicia eloquent
        $query = CursosInscripcions::query();

        // Quita las relaciones pre-programadas con $with en el modelo
        $query->setEagerLoads([]);
        $query->with('Curso');

        if($ciclo_id) { $query->filtrarCiclo($ciclo_id); }
        if($ciclo) { $query->filtrarCicloNombre($ciclo); }
        if($centro_id) { $query->filtrarCentro($centro_id); }
        if($anio) { $query->filtrarAnio($anio); }

        //$query->filtrarEstadoInscripcion('CONFIRMADA');

        // Es necesario quitar al curso Otro?...
        $query->whereHas('Curso', function ($cursos) {
            return $cursos->where('turno', '<>','Otro');
        });

        $agrupado = $query->get()->groupBy('Curso.id');

        $output = [];
        foreach($agrupado as $curso_id => $ins) {
            $curso = $ins->first()->curso;

            $plazas = $curso->plazas;
            $matricula = $curso->matricula;
            $vacantes = $curso->vacantes;

            $new_plazas = $plazas;
            $new_matricula = count($ins);
            $new_vacantes = $new_plazas - $new_matricula;

            $updcurso = Cursos::find($curso_id);
            $updcurso->plazas = $new_plazas;
            $updcurso->matricula = $new_matricula;
            $updcurso->vacantes = $new_vacantes;
            $updcurso->save();

            $map = [
                'curso_id' => $curso_id,
                'old' => [
                    'plazas' => $plazas,
                    'matricula' => $matricula,
                    'vacantes' => $vacantes,
                ],
                'new' => [
                    'plazas' => $new_plazas,
                    'matricula' => $new_matricula,
                    'vacantes' => $new_vacantes,
                ]
            ];

            $output[] = $map;
        }

        return $output;
    }

    // Agrupa las secciones con division, suma el total de plazas, matriculas, y vacantes para luego
    // las guardar en la seccion respectiva sin division
    private function normalizacionDeCursosSinDivision() {
        $cursos = Cursos::with('Centro')
            ->where('division', '')
            ->where('turno', '<>', 'Otro')
            //->where('turno', 'tarde')
            //->where('centro_id', '118')
            //->where('anio', 'Sala de 4 años')
            ->get();

        $output = [];
        foreach ($cursos as $curso) {

            // Unidades en plazas, matriculas y vacantes de cursos sin division del centro
            $recuento = $this->cuantificarMatriculaConDivisionDesdeCursos(
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

    private function cuantificarMatriculaConDivisionDesdeCursos($centro_id,$anio,$turno)
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