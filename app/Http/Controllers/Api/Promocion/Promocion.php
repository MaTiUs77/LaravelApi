<?php
namespace App\Http\Controllers\Api\Promocion;

use App\Centros;
use App\Ciclos;
use App\Cursos;
use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use App\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Promocion extends Controller
{
    public function start(Request $request)
    {
        // Ciclo hard-codeado
        $ciclo_actual = Ciclos::where('nombre',2017)->first();
        $ciclo_siguiente = Ciclos::where('nombre',2018)->first();

        // Json enviado en el request de la ruta
        $data = $request->json();

        $ids = $data->get('id');
        $centro_id = $data->get('centro_id');
        $curso_id = $data->get('curso_id');
        $curso_id_promocion = $data->get('curso_id_promocion');
        $user_id = $data->get('user_id');

        // Obtengo datos de las inscripciones a promocionar
        $inscripciones =  Inscripcions::whereIn('id',$ids)
            ->get();

        $user =  Users::where('id',$user_id)->first();
        $centro =  Centros::where('id',$centro_id)->first();
        $cursoFrom =  Cursos::where('id',$curso_id)->first();
        $cursoTo =  Cursos::where('id',$curso_id_promocion)->first();

        $movimientosLog = "";
        // Genero nuevas inscripciones modificando solo algunos datos de la inscripcion anterior
        foreach($inscripciones as $inscripcion)
        {
            // Copia de el registro de inscripcion
            $promocion = $inscripcion->replicate();
            
            // Si la inscripcion se encuentra en el 2018, no se promociona,... 
            if($promocion->ciclo_id == $ciclo_actual->id)
            {
                // Modifico algunos campos antes de crear la inscripcion nueva para el ciclo siguiente
                $promocion->legajo_nro = $this->nuevoLegajo($promocion);
                $promocion->ciclo_id = $ciclo_siguiente->id;
                $promocion->usuario_id = $user_id;
                $promocion->promocionado = 0;
                $promocion->save();

                // Una vez realizada la nueva inscripcion, guardo el ID generado en CursoInscripcion
                $cursoInscripcion = new CursosInscripcions();
                $cursoInscripcion->curso_id = $curso_id_promocion;
                $cursoInscripcion->inscripcion_id = $promocion->id;
                $cursoInscripcion->save();

                // Activo el flag de promocionado en el ciclo actual
                $inscripcion->promocionado = 1;
                $inscripcion->save();

                $movimientosLog .= "
                Inscripcion_id: $inscripcion->id => $promocion->id
                Ciclo_id: $inscripcion->ciclo_id => $promocion->ciclo_id
                Legajo: $inscripcion->legajo_nro =>  $promocion->legajo_nro
                ";
            } else 
            {
                $warningLog= "
                Inscripcion_id: $inscripcion->id
                Ciclo_id: $inscripcion->ciclo_id != $ciclo_actual->id 
                Legajo: $inscripcion->legajo_nro 
                NO SE PROMOCIONA, YA ESTA INSCRIPTO, DEBERIA EDITARSE
                ";

                Log::warning("($user->id) $user->username :: PROMOCION :: ($centro->id)$centro->nombre
                    Division: $cursoFrom->anio $cursoFrom->division $cursoFrom->turno => $cursoTo->anio $cursoTo->division $cursoTo->turno 
        
                    $warningLog
                ");

                $movimientosLog .= $warningLog;
            }
        }

        Log::info("($user->id) $user->username :: PROMOCION :: ($centro->id)$centro->nombre
            Division: $cursoFrom->anio $cursoFrom->division $cursoFrom->turno => $cursoTo->anio $cursoTo->division $cursoTo->turno 

            $movimientosLog
        ");

        return [
            'done' => true
        ];
    }

    private function nuevoLegajo(Inscripcions $inscripcion)
    {
        list($dni,$ciclo) = explode('-',$inscripcion->legajo_nro);
//        $nuevoCiclo = (int) $ciclo + 1;
        // CICLO 2018 FORZADO
        $nuevoCiclo = 18;

        return "$dni-$nuevoCiclo";
    }
}
