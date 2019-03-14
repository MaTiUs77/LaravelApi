<?php

namespace App\Http\Controllers\Api\Exportar\v1\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ListaAlumnosResource extends Resource
{
    public function toArray($request)
    {
        $inscripcion = $this['inscripcion'];
        $alumno= $inscripcion['alumno'];
        $persona=  collect($alumno['persona']);

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
        $direccion = trim(join(' ',$direccion));

        return [
            'nombre_completo' => $persona['nombre_completo'],
            'documento_tipo' => $persona['documento_tipo'],
            'documento_nro' => $persona['documento_nro'],
            'telefono_nro' => $persona['telefono_nro'],
            'direccion' => $direccion
        ];
    }
}