<?php

namespace App\Resources;

use App\Ciclos;
use App\Inscripcions;
use Illuminate\Http\Resources\Json\Resource;

class RepitenciaResource extends Resource
{
    public function toArray($request)
    {
        $curso=  collect($this['curso']);
        $inscripcion = collect($this['inscripcion']);
        $alumno= collect($inscripcion['alumno']);
        $persona=  collect($alumno['persona']);

        $desde= $curso->only([
            'anio','division','turno'
        ]);

        $desde['centro'] = collect($inscripcion['centro'])->only([
            'id','cue','nombre','sigla','nivel_servicio','sector'
        ]);

        // Obtener curso de repitencia
        $hacia = null;
        if(isset($inscripcion['repitencia'])){
            $hacia= collect($inscripcion['repitencia']['curso']);
            $hacia= $hacia->first();
            $hacia= collect($hacia)->only([
                'anio','division','turno','centro_id'
            ]);
            $hacia['centro'] = collect($inscripcion['repitencia']['centro'])->only([
                'id','cue','nombre','sigla','nivel_servicio','sector'
            ]);

        }

        $inscripcion = $inscripcion->only([
            "id", "legajo_nro", "estado_inscripcion", "ciclo_id", "centro_id",
            "repitencia_id"
        ]);

        $inscripcion['alumno_id'] = $alumno->get('id');
        $inscripcion['persona'] = $persona->only([
            "id","nombre_completo"
        ]);

        return compact('inscripcion','desde','hacia');
    }
}