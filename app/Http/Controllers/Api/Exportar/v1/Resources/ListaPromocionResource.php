<?php

namespace App\Http\Controllers\Api\Exportar\v1\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ListaPromocionResource extends Resource
{
    public function toArray($request)
    {
        $promocion = null;

        $inscripcion = $this['inscripcion'];
        $curso = collect($this['curso']);
        $alumno = collect($inscripcion['alumno']);
        $persona = collect($alumno['persona']);

        if(isset($inscripcion['promocion'])){
            $promocion  = collect($inscripcion['promocion']['curso']);
            $promocion = $promocion->only([
                'anio','division','turno','centro_id'
            ]);
        }

        $inscripcion = $inscripcion->only([
            "id", "legajo_nro", "estado_inscripcion", "ciclo_id", "centro_id"
        ]);

        $inscripcion['alumno_id'] = $alumno->get('id');
        $inscripcion['persona'] = $persona->only([
            "id","nombre_completo"
        ]);

        $curso = $curso->only([
            'anio','division','turno','centro_id'
        ]);


        return compact('inscripcion','curso','promocion');
    }
}