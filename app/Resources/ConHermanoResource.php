<?php

namespace App\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class ConHermanoResource extends Resource
{
    public function toArray($request)
    {

        $inscripcion = $this['inscripcion'];
        $alumno= $inscripcion['alumno'];
        $persona=  collect($alumno['persona']);
        $familiares =  collect($alumno['familiares']);

        $centro = collect($inscripcion['centro']);
        $ciudad = collect($centro['ciudad']);

        // Hermano
        $hermano= $inscripcion['hermano'];
        $hermano_persona=  collect($hermano['persona']);
        $hermano_familiares =  collect($hermano['familiares']);

        $response = [];
        $response['inscripcion'] = [
            'ciudad' => $ciudad['nombre'],
            'centro' => $centro['nombre'],
            'documento_nro' => $persona['documento_nro'],
            'nombre_completo' => $persona['nombre_completo'],
            'fecha_nac' => Carbon::parse($persona['fecha_nac'])->format('d/m/Y'),
            'telefono_nro' => $persona['telefono_nro'],
            'direccion' => $this->transformDireccion($persona),
            'familiares' => $this->padresConfirmados($familiares)
        ];

        $response['hermano'] = null;
        if(isset($hermano_persona['id'])) {
            $response['hermano'] = [
                'documento_nro' => $hermano_persona['documento_nro'],
                'nombre_completo' => $hermano_persona['nombre_completo'],
                'fecha_nac' => Carbon::parse($hermano_persona['fecha_nac'])->format('d/m/Y'),
                'telefono_nro' => $hermano_persona['telefono_nro'],
                'direccion' => $this->transformDireccion($hermano_persona),
                'familiares' => $this->padresConfirmados($hermano_familiares)
            ];
        }

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
            $familiar = "{$value['familiar']['persona']['nombre_completo']} ({$value['familiar']['vinculo']})";
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