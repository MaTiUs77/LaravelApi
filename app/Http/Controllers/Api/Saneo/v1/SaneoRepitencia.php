<?php
namespace App\Http\Controllers\Api\Saneo\v1;

use App\Ciclos;
use App\CursosInscripcions;
use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use App\Resources\RepitenciaResource;
use Illuminate\Support\Facades\Log;

class SaneoRepitencia extends Controller
{
    private $version = '4';

    private $cicloActual = null;
    private $cicloAnterior= null;

    public function start($ciclo=2019,$page=1,$por_pagina=10)
    {
        if(request('page')) {
            $page = request('page');
        }

        if(request('por_pagina')) {
            $por_pagina = request('por_pagina');
        }

        $cicloActual = $ciclo;
        $cicloAnterior= $ciclo - 1;

        $this->cicloActual = Ciclos::where('nombre',$cicloActual)->first();
        $this->cicloAnterior = Ciclos::where('nombre',$cicloAnterior)->first();

        $params = [
            'ciclo' => $ciclo,
            'division' => 'con',
            'estado_inscripcion' => 'CONFIRMADA',
            'nivel_servicio' => ['Comun - Primario','Comun - Secundario'],
            'promocion' => 'sin',
            'repitencia' => 'sin',
            'with' => 'inscripcion.curso,inscripcion.centro',

            'por_pagina' => $por_pagina,
            'page' => $page,
        ];

        Log::info("=============================================================================");
        Log::info("SaneoRepitencia: Version ".$this->version);
        Log::info("SaneoRepitencia::start($ciclo,$page,$por_pagina)");
        Log::info("SaneoRepitencia::consume->/api/v1/inscripcion/lista");

        // Consumo API Inscripciones
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        Log::info("SaneoRepitencia:doSaneo()");
        $this->doSaneo($response);
        $response['data'] = RepitenciaResource::collection(collect($response['data']));
        Log::info("SaneoRepitencia:Finalizado page: ".$page." last_page: ".$response['last_page']);
        return $response;
    }

    public function doSaneo($apiResponse) {
        foreach ($apiResponse['data'] as $item) {
            $this->sanearInscripcion($item);
        }
    }

    public function sanearInscripcion($item)
    {
        $curso=  collect($item['curso']);
        $inscripcion = $item['inscripcion'];
        $alumno= $inscripcion['alumno'];
        $persona=  collect($alumno['persona']);

        $anterior = null;
        $actual = [
            'trazabilidad' => [
                'repitencia_id' => $inscripcion['repitencia_id'],
                'promocion_id' => $inscripcion['promocion_id'],
            ],

            'ciclo_id' => $inscripcion['ciclo_id'],
            'inscripcion_id' => $inscripcion['id'],
            'centro_id' => $inscripcion['centro_id'],
            'alumno_id' => $alumno['id'],
            'legajo_nro' => $inscripcion['legajo_nro'],
            'estado_inscripcion' => $inscripcion['estado_inscripcion'],
            'curso' => $curso->only(['id','tipo','anio','division','turno']),
        ];

        // Obtiene datos de la inscripcion anterior
        $anterior= $this->obtenerInscripcionAnterior($inscripcion,$persona);

        // Sanea la DB, establece la relacion entre la inscripcion actual y verifica si la
        // inscripcion anterior fue una repitencia o una promocion
        $cursoActual = $actual['curso'];
        $cursoAnterior = $anterior['curso'];

        if(
            !empty($cursoActual['division']) &&
            !empty($cursoAnterior['division'])
        ) {
            if($cursoActual['anio'] == $cursoAnterior['anio']) {
                // Repitencia detectada
                $inscripcionRepitente = Inscripcions::findOrFail($anterior['inscripcion_id']);
                $inscripcionRepitente->repitencia_id = $actual['inscripcion_id'];
                $inscripcionRepitente->save();

                $anterior['trazabilidad']['repitencia_id'] = $inscripcionRepitente->repitencia_id;
                $anterior['saneo']['repitencia_id'] = $inscripcionRepitente->repitencia_id;
            } else {
                // Promocion detectada
                $inscripcionPromocionada = Inscripcions::findOrFail($anterior['inscripcion_id']);
                $inscripcionPromocionada->promocion_id = $actual['inscripcion_id'];
                $inscripcionPromocionada->save();

                $anterior['trazabilidad']['promocion_id'] = $inscripcionPromocionada->promocion_id;
                $anterior['saneo']['promocion_id'] = $inscripcionPromocionada->promocion_id;
            }
        }

        $output = [
            'persona' => $persona,
            'actual' => $actual,
            'anterior' => $anterior,
        ];

        $this->confirmarSaneo($output);

        return $output;
    }

    private function obtenerInscripcionAnterior($inscripcion,$persona)
    {
        $inscripcionAnteriorQuery = CursosInscripcions::with(['inscripcion.alumno','inscripcion.curso'])
            ->filtrarPersona($persona['id'])
            ->filtrarEstadoInscripcion(['CONFIRMADA','EGRESO'])
            ->filtrarDivision('con')
            ->filtrarCiclo($this->cicloAnterior->id)
        ;

        // Verifica cuantas inscripciones anteriores se registraron en estado CONFIRMADA y EGRESO
        $inscripcionesFound = $inscripcionAnteriorQuery->count();

        // Obtiene solo la ultima inscripcion
        // (IMPORTANTE, ORDENAR SEGUN CORRESPONDA, PUEDE NO SER LA ULTIMA INSCRIPCION DEL CICLO)
        $inscripcionAnterior = $inscripcionAnteriorQuery->first();
        $inscripcionAnterior = $inscripcionAnterior['inscripcion'];

        $response = [
            'trazabilidad' => [
                'repitencia_id' => $inscripcionAnterior['repitencia_id'],
                'promocion_id' => $inscripcionAnterior['promocion_id'],
            ],

            'inscripciones_found' => $inscripcionesFound,

            'ciclo_id' => $inscripcionAnterior['ciclo_id'],
            'inscripcion_id' => $inscripcionAnterior['id'],
            'centro_id' => $inscripcionAnterior['centro_id'],
            'alumno_id' => $inscripcionAnterior['alumno_id'],
            'legajo_nro' => $inscripcionAnterior['legajo_nro'],
            'estado_inscripcion' => $inscripcionAnterior['estado_inscripcion'],
        ];

        $response['curso'] = null;

        if(isset($inscripcionAnterior['curso'])) {
            $cursoAnterior = $inscripcionAnterior['curso']->first();
            $response['curso'] = $cursoAnterior->only(['id','tipo','anio','division','turno']);
        }

        return $response;
    }

    private function confirmarSaneo($output)
    {
        $inscripcion_id_actual = $output['actual']['inscripcion_id'];
        $inscripcion_id_anterior = $output['anterior']['inscripcion_id'];

        $curso_actual = collect($output['actual']['curso']);
        $seccion_actual = join(', ',$curso_actual->only('anio','division','turno','tipo')->toArray());

        // Un poco de logueo
        $msg[] = "ID: $inscripcion_id_actual";
        $msg[] = "PERSONA_ID: ".$output['persona']['id'];
        $msg[] = "NOMBRE: ".$output['persona']['nombre_completo'];

        // Existen inscripciones anteriores, segun el filtro solicitado?
        if($inscripcion_id_anterior) {
            $repitencia_id = $output['anterior']['trazabilidad']['repitencia_id'];
            $promocion_id = $output['anterior']['trazabilidad']['promocion_id'];

            $curso_anterior = collect($output['anterior']['curso']);
            $seccion_anterior = join(', ',$curso_anterior->only('anio','division','turno','tipo')->toArray());

            $inscripciones_found = $output['anterior']['inscripciones_found'];
            if($inscripciones_found>1) {
                $inscripciones_found = $inscripciones_found."(MULTIPLES)";
            }

            $msg[] = "FOUND: ".$inscripciones_found;

            if($repitencia_id!=null) {
                $msg[] = "ESTADO: REPITIO";
                $msg[] = "ID_ANTERIOR:$inscripcion_id_anterior($seccion_anterior)";
                $msg[] = "ID_ACTUAL:$repitencia_id($seccion_actual)";
            }
            if($promocion_id!=null) {
                $msg[] = "ESTADO: PROMOCIONO";
                $msg[] = "ID_ANTERIOR:$inscripcion_id_anterior($seccion_anterior)";
                $msg[] = "ID_ACTUAL:$promocion_id($seccion_actual)";
            }
        } else {
            // En caso de no haber localizado inscripciones anteriores con estado CONFIRMADA o EGRESO
            // Procedemos a obtener las inscripciones sin filtro de estado o division del ciclo anterior
            $params = [
                'ciclo_id'=> $this->cicloAnterior->id,
                'with'=>'alumnos.inscripciones.curso'
            ];

            // Consumo API Personas
            $api = new ApiConsume();
            $api->get("personas/".$output['persona']['id'],$params);
            if($api->hasError()) {
                // no se localizaron inscripciones en el ciclo anterior
                $msg[] = "ESTADO: COMPLETAMENTE NUEVA";
            } else {
                $verifyPersona= $api->response();
                // Obtiene todas las inscripciones de esa Persona en el ciclo solicitado
                $alumnos = collect($verifyPersona['alumnos']);
                $inscripciones_found= $alumnos->count('inscripciones');

                $inscripciones = $alumnos->map( function($k){ return $k['inscripciones']; });
                $inscripciones = $inscripciones->collapse();

                $status = $inscripciones->map( function($k){
                    return collect($k)->only(['id','estado_inscripcion']);
                });

                $msg[] = "FOUND_ALL: $inscripciones_found";
                $msg[] = "DEBUG: ".$status;
            }
        }

        Log::info(join('|',$msg));
    }
}