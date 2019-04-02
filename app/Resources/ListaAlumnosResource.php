<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ListaAlumnosResource extends Resource
{
    public function toArray($request)
    {
        $inscripcion = $this['inscripcion'];
        $alumno= $inscripcion['alumno'];
        $persona=  collect($alumno['persona']);

        $response = [
            'nombre_completo' => $persona['nombre_completo'],
            'documento_tipo' => $persona['documento_tipo'],
            'documento_nro' => $persona['documento_nro'],
            'telefono_nro' => $persona['telefono_nro'],
            'direccion' => $this->transformDireccion($persona)
        ];

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