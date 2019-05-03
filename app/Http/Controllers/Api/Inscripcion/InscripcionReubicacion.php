<?php
namespace App\Http\Controllers\Api\Inscripcion;

use App\Ciclos;
use App\Cursos;
use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InscripcionReubicacion extends Controller
{
    public $validationRules = [
        'id' => 'required|array',
        'user_id' => 'required|numeric',
        'curso_id' => 'required|numeric',
        'curso_id_to' => 'required|numeric',
    ];

    public function start(Request $request)
    {
        // Se validan los parametros
        $validator = Validator::make($request->all(), $this->validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $user =  User::where('id',$request->get('user_id'))->first();
        $cursoInscripcion =  CursosInscripcions::whereIn('inscripcion_id',$request->get('id'))->get();

        $cursoFrom=  Cursos::where('id',$request->get('curso_id'))->first();
        $cursoTo=  Cursos::where('id',$request->get('curso_id_to'))->first();
        $cicloActual = Ciclos::where('nombre',Carbon::now()->year)->first();

        // Solo se permite reubicar cursos del mismo año
        //if($cursoFrom->anio == $cursoTo->anio)
        //{
            foreach($cursoInscripcion as $curins)
            {
                // Se edita el curso anterior por el nuevo
                $curins->curso_id = $cursoTo->id;
                $curins->save();

                // Se realiza la cuantificacion en matricula y vacantes de ambos cursos
                $this->cuantificarInscripcion($cursoFrom,$cursoTo,$cicloActual);

                $success[] = $curins->Inscripcion->id;
            }

            $output = [
                'success'=>$success
            ];

            Log::info("Reubicacion ({$user->id}) {$user->username} | {$cursoFrom->id} => {$cursoTo->id}",$output);
        /*
        } else {
            $output = [
                'error'=>"Los años son diferentes, no se puede realizar la reubicacion"
            ];

            Log::warning("Reubicacion ({$user->id}) {$user->username} | {$cursoFrom->id} => {$cursoTo->id}",$output);
        }
        */
        return $output;
    }

    private function cuantificarInscripcion(Cursos $cursoFrom, Cursos $cursoTo, Ciclos $cicloActual) {
        $fromMatriculasCount = CursosInscripcions::where('curso_id',$cursoFrom->id)
            ->filtrarCiclo($cicloActual->id)
            ->count();

        $cursoFrom->matricula = $fromMatriculasCount;
        $cursoFrom->vacantes = $cursoFrom->plazas - $fromMatriculasCount;
        $cursoFrom->save();

        $toMatriculasCount = CursosInscripcions::where('curso_id',$cursoTo->id)
            ->filtrarCiclo($cicloActual->id)
            ->count();

        $cursoTo->matricula = $toMatriculasCount;
        $cursoTo->vacantes = $cursoTo->plazas - $toMatriculasCount;
        $cursoTo->save();
    }
}
