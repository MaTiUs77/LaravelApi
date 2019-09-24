<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PromocionResource extends Resource
{
    public function toArray($request)
    {
        // Cursada actual
        $inscripcion = collect($this['inscripcion']);
        $curso=  collect($this['curso']);
        $centro=  collect($inscripcion['centro']);
        $centro = $centro->only([
            'id','cue','nombre','sigla','sector','nivel_servicio'
        ]);

        // Datos de alumno y persona de la inscripcion
        $alumno= collect($inscripcion['alumno']);
        $persona=  collect($alumno['persona']);

        $curso = $curso->only([
            'id','anio','division','turno','centro_id'
        ]);

        // Obtener curso de repitencia
        $promocion = [
            'centro' =>null,
            'curso' =>null
        ];

        if(isset($inscripcion['promocion'])){
            $cursoSiguiente = collect($inscripcion['promocion']['curso']);
            $cursoSiguiente = $cursoSiguiente->first();
            $cursoSiguiente= collect($cursoSiguiente)->only([
                'id','anio','division','turno','centro_id'
            ]);
            $centroSiguiente =  collect($inscripcion['promocion']['centro']);
            $centroSiguiente = $centroSiguiente->only([
                'id','cue','nombre','sigla','sector','nivel_servicio'
            ]);

            $promocion = [
                'centro' => $centroSiguiente,
                'curso' => $cursoSiguiente,
            ];
        }

        $inscripcion = $inscripcion->only([
            "id", "legajo_nro", "estado_inscripcion", "ciclo_id", "centro_id",
            "promocion_id"
        ]);

        $inscripcion['alumno_id'] = $alumno->get('id');
        $inscripcion['persona'] = $persona->only([
            "id","nombre_completo"
        ]);

        $actual = compact('centro','curso');
        $anterior = $promocion;

        return compact('inscripcion','actual','anterior');
    }
}