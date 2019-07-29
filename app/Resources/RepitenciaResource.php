<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RepitenciaResource extends Resource
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
            'anio','division','turno','centro_id'
        ]);

        // Obtener curso de repitencia
        $repitencia = [
            'centro' =>null,
            'curso' =>null
        ];
        if(isset($inscripcion['repitencia'])){
            $cursoAnterior = collect($inscripcion['repitencia']['curso']);
            $cursoAnterior = $cursoAnterior->first();
            $cursoAnterior = collect($cursoAnterior)->only([
                'anio','division','turno','centro_id'
            ]);
            $centroAnterior =  collect($inscripcion['repitencia']['centro']);
            $centroAnterior = $centroAnterior->only([
                'id','cue','nombre','sigla','sector','nivel_servicio'
            ]);

            $repitencia = [
                'centro' => $centroAnterior,
                'curso' => $cursoAnterior,
            ];
        }

        $inscripcion = $inscripcion->only([
            "id", "legajo_nro", "estado_inscripcion", "ciclo_id", "centro_id",
            "repitencia_id"
        ]);

        $inscripcion['alumno_id'] = $alumno->get('id');
        $inscripcion['persona'] = $persona->only([
            "id","nombre_completo"
        ]);

        $actual = compact('centro','curso');
        $anterior = $repitencia;

        return compact('inscripcion','actual','anterior');
    }
}