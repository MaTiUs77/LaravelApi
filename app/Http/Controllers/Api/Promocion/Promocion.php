<?php
namespace App\Http\Controllers\Api\Promocion;

use App\Centros;
use App\Ciclos;
use App\Cursos;
use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Promocion extends Controller
{
    public $validationRules = [
        'id' => 'required|array',
        'centro_id' => 'required|numeric',
        'curso_id' => 'required|numeric',
        'curso_id_promocion' => 'required|numeric',
        'user_id' => 'required|numeric',
    ];

    private $user;
    private $centro;
    private $cursoFrom;
    private $cursoTo;
    private $cicloFrom;
    private $cicloTo;

    private $infoLog;

    public function start(Request $request)
    {
        $cicloActual = Carbon::now()->format('Y');
        $cicloSiguiente = $cicloActual+1;

        // Se validan los parametros
        $validator = Validator::make($request->all(), $this->validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        // Se transforman los parametros a json
        $data = $request->json();

        $ids = $data->get('id');
        $centro_id = $data->get('centro_id');
        $curso_id = $data->get('curso_id');
        $curso_id_promocion = $data->get('curso_id_promocion');
        $user_id = $data->get('user_id');

        // Obtengo datos de las inscripciones a promocionar
        $inscripciones =  Inscripcions::whereIn('id',$ids)->get();

        // Ciclo hard-codeado
        $this->cicloFrom = Ciclos::where('nombre',$cicloActual)->first();
        $this->cicloTo = Ciclos::where('nombre',$cicloSiguiente)->first();

        $this->user =  User::where('id',$user_id)->first();
        $this->centro =  Centros::where('id',$centro_id)->first();
        $this->cursoFrom =  Cursos::where('id',$curso_id)->first();
        $this->cursoTo =  Cursos::where('id',$curso_id_promocion)->first();

        // Genero nuevas inscripciones modificando solo algunos datos de la inscripcion anterior
        Log::debug("Verificando promociones total: {$inscripciones->count()}");
        foreach($inscripciones as $inscripcion)
        {
            // Copia de el registro de inscripcion
            $promocion = $inscripcion->replicate();

            // Si el ciclo de la inscripcion a promocionar corresponde al ciclo_from.. continuo
            if($promocion->ciclo_id == $this->cicloFrom->id)
            {
                // Modifico algunos campos antes de crear la inscripcion nueva para el ciclo siguiente
                $nuevoLegajo = $this->nuevoLegajo($promocion);

                // No agrega la promocion, si el legajo ya existe
                $findLegajo =  Inscripcions::where('legajo_nro',$nuevoLegajo)->first();
                Log::debug("Localizando existencia del nuevo legajo: $nuevoLegajo");
                if($findLegajo==null)
                {
                    Log::debug("No localizando creando nuevo legajo: $nuevoLegajo");

                    $promocion->legajo_nro = $nuevoLegajo;
                    $promocion->ciclo_id = $this->cicloTo->id;
                    $promocion->usuario_id = $this->user->id;
                    $promocion->promocionado = null; // Deprecar
                    $promocion->promocion_id = null;
                    $today = Carbon::now();
                    $promocion->created = $today;
                    $promocion->modified = $today;
                    $promocion->save();

                    // Una vez realizada la nueva inscripcion, guardo el ID generado en CursoInscripcion
                    $cursoInscripcion = new CursosInscripcions();
                    $cursoInscripcion->curso_id = $this->cursoTo->id;
                    $cursoInscripcion->inscripcion_id = $promocion->id;
                    $cursoInscripcion->save();

                    // Para guardar el id de la nueva promocion es necesario
                    // cambiar la columna promocionado de TINYINT a INT 11 para guardar $cursoInscripcion->id;
                    $inscripcion->promocionado = $cursoInscripcion->id; // Deprecar
                    $inscripcion->promocion_id = $promocion->id;
                    $inscripcion->save();

                    $this->cuantificarInscripcion($cursoInscripcion);

                    Log::debug("
                    Inscripcion_id: {$inscripcion->id} => {$promocion->id}
                    Ciclo_id: {$inscripcion->ciclo_id} => {$promocion->ciclo_id}
                    Legajo: {$inscripcion->legajo_nro} => {$promocion->legajo_nro}
                    CursoInscripcion: {$cursoInscripcion->id}
                    ");
                } else {
                    Log::debug("Legajo localizado: $nuevoLegajo, no se realiza la promocion.");

                    Log::debug("
                    Inscripcion_id: {$inscripcion->id}
                    Ciclo_id: {$inscripcion->ciclo_id} => {$this->cicloTo->id}
                    Legajo: {$inscripcion->legajo_nro} => {$findLegajo->legajo_nro}
                    NO SE PROMOCIONA, EL LEGAJO YA EXISTE EN EL CICLO SIGUIENTE
                    ");
                }
            } else {
                Log::debug("
                Inscripcion_id: {$inscripcion->id}
                Ciclo_id: {$inscripcion->ciclo_id} != {$this->cicloFrom->id}
                Legajo: {$inscripcion->legajo_nro}
                NO SE PROMOCIONA, EL CICLO DE LA PROMOCION NO ES IGUAL AL CICLO ACTUAL
                ");
            }
        }

        return [
            'done' => true
        ];
    }

    private function logDebug($message) {
        Log::debug("({$this->user->id}) {$this->user->username} :: PROMOCION :: ({$this->centro->id}){$this->centro->nombre}
            Division: {$this->cursoFrom->anio} {$this->cursoFrom->division} {$this->cursoFrom->turno} => {$this->cursoTo->anio} {$this->cursoTo->division} {$this->cursoTo->turno}
            {$message}
        ");

    }

    // Genera el legajo en base al legajo anterior + ultimos 2 digitos del ciclo siguiente
    private function nuevoLegajo(Inscripcions $promocion)
    {
        // Para 2018 devuelve 18
        $nuevoCiclo = substr( $this->cicloTo->nombre, -2);
        list($dni,$ciclo) = explode('-',$promocion->legajo_nro);

        return "$dni-$nuevoCiclo";
    }

    private function cuantificarInscripcion(CursosInscripcions $cursoInscripcion) {
        $cuantificar = Cursos::where('id',$cursoInscripcion->curso_id)->first();
        $cuantificar->matricula++;
        $cuantificar->vacantes--;
        $cuantificar->save();
    }
}
