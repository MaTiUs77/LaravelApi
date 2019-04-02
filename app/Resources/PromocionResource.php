<?php

namespace App\Resources;

use App\Ciclos;
use App\Inscripcions;
use Illuminate\Http\Resources\Json\Resource;

class PromocionResource extends Resource
{
    public function toArray($request)
    {
        $curso=  collect($this['curso']);
        $inscripcion = collect($this['inscripcion']);
        $alumno= collect($inscripcion['alumno']);
        $persona=  collect($alumno['persona']);

        $curso = $curso->only([
            'anio','division','turno','centro_id'
        ]);

        // Obtener curso de promocion
        $promocion = null;
        if(isset($inscripcion['promocion'])){
            $promocion  = collect($inscripcion['promocion']['curso']);
            $promocion = $promocion->only([
                'anio','division','turno','centro_id'
            ]);
        }

        $inscripcion = $inscripcion->only([
            "id", "legajo_nro", "estado_inscripcion", "ciclo_id", "centro_id",
            "promocion_id","repitencia_id"
        ]);

        $inscripcion['alumno_id'] = $alumno->get('id');
        $inscripcion['persona'] = $persona->only([
            "id","nombre_completo"
        ]);

        return compact('inscripcion','curso','promocion');
    }
}