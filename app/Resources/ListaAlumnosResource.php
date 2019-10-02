<?php

namespace App\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class ListaAlumnosResource extends Resource
{
    public function toArray($request)
    {
        $inscripcion = $this['inscripcion'];
        $alumno= $inscripcion['alumno'];
        $persona=  collect($alumno['persona']);
        $familiares =  collect($alumno['familiares']);

        $response = [
            'documento_nro' => $persona['documento_nro'],
            'nombre_completo' => $persona['nombre_completo'],
            'fecha_nac' => Carbon::parse($persona['fecha_nac'])->format('d/m/Y'),
            'edad' => $persona['edad'],
            'nacionalidad' => $persona['nacionalidad'],
            'telefono_nro' => $persona['telefono_nro'],
            'direccion' => $this->transformDireccion($persona),
            'email' => $persona['email'],
            'familiares' => $this->padresConfirmados($familiares)
        ];

        return $response;
    }

    private function padresConfirmados($familiares)
    {
        // Se filtran los padres/tutores confirmados
        $padresConfirmados = $familiares->filter(function ($value) {
            return ($value['status'] == 'confirmada');
        });

        // Se obtiene el nombre y el vinculo
        return $padresConfirmados->map(function($value){
            $familiar = "{$value['familiar']['persona']['nombre_completo']} ({$value['familiar']['vinculo']}) | {$value['familiar']['persona']['telefono_nro']} | {$value['familiar']['persona']['telefono_nro_alt']} | {$value['familiar']['persona']['email']}";
            return $familiar;
        });
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