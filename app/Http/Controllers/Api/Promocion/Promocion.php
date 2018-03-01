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

    public $validationMessages = [
        'required' => 'El :attribute es requerido.',
        'numeric' => 'El :attribute debe ser numerico',
        'string' => 'El :attribute debe ser solo texto',
        'accepted' => 'El :attribute debe ser: 1, on o true',
        'array' => 'El :attribute debe ser un array',
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
        // Se validan los parametros
        $validator = Validator::make($request->all(), $this->validationRules,$this->validationMessages);
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
        $this->cicloFrom = Ciclos::where('nombre',2017)->first();
        $this->cicloTo = Ciclos::where('nombre',2018)->first();

        $this->user =  Users::where('id',$user_id)->first();
        $this->centro =  Centros::where('id',$centro_id)->first();
        $this->cursoFrom =  Cursos::where('id',$curso_id)->first();
        $this->cursoTo =  Cursos::where('id',$curso_id_promocion)->first();

        // Genero nuevas inscripciones modificando solo algunos datos de la inscripcion anterior
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
                if($findLegajo==null)
                {
                    $promocion->legajo_nro = $nuevoLegajo;
                    $promocion->ciclo_id = $this->cicloTo->id;
                    $promocion->usuario_id = $this->user->id;
                    $promocion->promocionado = 0;
                    $promocion->save();

                    // Una vez realizada la nueva inscripcion, guardo el ID generado en CursoInscripcion
                    $cursoInscripcion = new CursosInscripcions();
                    $cursoInscripcion->curso_id = $this->cursoTo->id;
                    $cursoInscripcion->inscripcion_id = $promocion->id;
                    $cursoInscripcion->save();

                    // Para guardar el id de la nueva promocion es necesario
                    // cambiar la columna promocionado de TINYINT a INT 11 para guardar $cursoInscripcion->id;
                    $inscripcion->promocionado = $cursoInscripcion->id;
                    $inscripcion->save();

                    $this->cuantificarInscripcion($cursoInscripcion);

                    $this->infoLog .= "
                    Inscripcion_id: {$inscripcion->id} => {$promocion->id}
                    Ciclo_id: {$inscripcion->ciclo_id} => {$promocion->ciclo_id}
                    Legajo: {$inscripcion->legajo_nro} => {$promocion->legajo_nro}
                    CursoInscripcion: {$cursoInscripcion->id}
                    ";
                } else {
                    $message = "
                    Inscripcion_id: {$inscripcion->id}
                    Ciclo_id: {$inscripcion->ciclo_id} => {$this->cicloTo->id}
                    Legajo: {$inscripcion->legajo_nro} => {$findLegajo->legajo_nro}
                    NO SE PROMOCIONA, EL LEGAJO YA EXISTE EN EL CICLO SIGUIENTE
                    ";

                    $this->logWarning($message);
                }
            } else {
                $message = "
                Inscripcion_id: {$inscripcion->id}
                Ciclo_id: {$inscripcion->ciclo_id} => {$this->cicloFrom->id}
                Legajo: {$inscripcion->legajo_nro}
                NO SE PROMOCIONA, YA ESTA INSCRIPTO, DEBERIA EDITARSE
                ";

                $this->logWarning($message);
            }
        }

        $this->logInfo();

        return [
            'done' => true
        ];
    }

    private function logWarning($message) {
        $this->infoLog .= $message;

        Log::warning("({$this->user->id}) {$this->user->username} :: PROMOCION :: ({$this->centro->id}){$this->centro->nombre}
            Division: {$this->cursoFrom->anio} {$this->cursoFrom->division} {$this->cursoFrom->turno} => {$this->cursoTo->anio} {$this->cursoTo->division} {$this->cursoTo->turno}
            {$message}
        ");

    }

    private function logInfo() {
        Log::info("({$this->user->id}) {$this->user->username} :: PROMOCION :: ({$this->centro->id}){$this->centro->nombre}
            Division: {$this->cursoFrom->anio} {$this->cursoFrom->division} {$this->cursoFrom->turno} => {$this->cursoTo->anio} {$this->cursoTo->division} {$this->cursoTo->turno}
            {$this->infoLog}
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
