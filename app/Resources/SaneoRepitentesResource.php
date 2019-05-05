<?php

namespace App\Resources;

use App\Ciclos;
use App\Inscripcions;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Log;

class SaneoRepitentesResource extends Resource
{
    public function toArray($request)
    {
        $inscripcion = $this['inscripcion'];
        $curso=  collect($this['curso']);
        $alumno= $inscripcion['alumno'];
        $persona=  collect($alumno['persona']);

        $response = [
            'nombre_completo' => $persona['nombre_completo'],
            'documento_tipo' => $persona['documento_tipo'],
            'documento_nro' => $persona['documento_nro'],
            'telefono_nro' => $persona['telefono_nro'],
            'direccion' => $this->transformDireccion($persona),

            'actual' => [
                'trazabilidad' => [
                    'repitencia_id' => $inscripcion['repitencia_id'],
                    'promocion_id' => $inscripcion['promocion_id'],
                ],

                'inscripcion_id' => $inscripcion['id'],
                'centro_id' => $inscripcion['centro_id'],
                'alumno_id' => $alumno['id'],
                'legajo_nro' => $inscripcion['legajo_nro'],
                'estado_inscripcion' => $inscripcion['estado_inscripcion'],
                'curso' => $curso->only(['id','tipo','anio','division','turno']),
            ],
            'anterior' => $this->verificarRepitencia($inscripcion,$alumno)
        ];

        $cursoActual = $response['actual']['curso'];
        $cursoAnterior = $response['anterior']['curso'];

        if(
            !empty($cursoActual['division']) &&
            !empty($cursoAnterior['division'])
        ) {
            if($cursoActual['anio'] == $cursoAnterior['anio']) {
                // Repitencia detectada
                $inscripcionRepitente = Inscripcions::findOrFail($response['anterior']['inscripcion_id']);
                $inscripcionRepitente->repitencia_id = $response['actual']['inscripcion_id'];
                $inscripcionRepitente->save();

                $response['anterior']['trazabilidad']['repitencia_id'] = $inscripcionRepitente->repitencia_id;
            } else {
                // Promocion detectada
                $inscripcionPromocionada = Inscripcions::findOrFail($response['anterior']['inscripcion_id']);
                $inscripcionPromocionada->promocion_id = $response['actual']['inscripcion_id'];
                $inscripcionPromocionada->save();

                $response['anterior']['trazabilidad']['promocion_id'] = $inscripcionPromocionada->promocion_id;
            }
        }

        $this->logMsg($response);

        return $response;
    }

    private function logMsg($response)
    {
        $repitio = $response['anterior']['trazabilidad']['repitencia_id'];
        $promociono = $response['anterior']['trazabilidad']['promocion_id'];

        $inscripcion_id_actual = $response['actual']['inscripcion_id'];
        $inscripcion_id_anterior = $response['anterior']['inscripcion_id'];

        $msg[] = "ID: ".$inscripcion_id_actual;
        $msg[] = $response['nombre_completo'];

        if($repitio) {
            $msg[] = "--> Repitio: $inscripcion_id_anterior desde $repitio";
        }
        if($promociono) {
            $msg[] = "--> Promociono: $inscripcion_id_anterior hacia $promociono";
        }

        if(!$promociono && !$repitio) {
            $msg[] = "--> Nueva";
        }

        Log::info(join(', ',$msg));
    }

    private function verificarRepitencia($inscripcion,$alumno)
    {
        $cicloActualNombre = $inscripcion['ciclo']['nombre'];
        $cicloAnteriorNombre = $cicloActualNombre - 1;
        $cicloAnterior = Ciclos::where('nombre',$cicloAnteriorNombre)->first();

        $inscripcionAnterior = Inscripcions::with('cursosInscripcions')
            ->where('alumno_id',$alumno['id'])
            ->where('centro_id',$inscripcion['centro_id'])
            ->where('ciclo_id',$cicloAnterior->id)
            ->where('estado_inscripcion','CONFIRMADA')
            ->first();

        $response = [
            'trazabilidad' => [
                'repitencia_id' => $inscripcionAnterior['repitencia_id'],
                'promocion_id' => $inscripcionAnterior['promocion_id'],
            ],

            'inscripcion_id' => $inscripcionAnterior['id'],
            'centro_id' => $inscripcionAnterior['centro_id'],
            'alumno_id' => $inscripcionAnterior['alumno_id'],
            'legajo_nro' => $inscripcionAnterior['legajo_nro'],
            'estado_inscripcion' => $inscripcionAnterior['estado_inscripcion'],
        ];

        if(isset($inscripcionAnterior['cursosInscripcions'])) {
            $response['curso'] = $inscripcionAnterior['cursosInscripcions']['curso']->only(['id','tipo','anio','division','turno']);
        } else {
            $response['curso'] = null;
        }

        return $response;
    }

    private function transformDireccion($persona) {
        $direccion = [];
        if(!empty($persona['calle_nombre'])) {
            $direccion[] = trim(strtoupper($persona['calle_nombre']));
        }
        if(!empty($persona['calle_nro'])) {
            $direccion[] = trim($persona['calle_nro']);
        }
        if(!empty($persona['tira_edificio'])) {
            $direccion[] = "Tira: ".trim($persona['tira_edificio']);
        }
        if(!empty($persona['depto_casa'])) {
            $direccion[] = "Depto: ".trim($persona['depto_casa']);
        }
        return trim(join(' ',$direccion));
    }
}