<?php

namespace App\Traits;

trait WithInscripcionScopes {

    // Filtros de INSCRIPCION
    function scopeFiltrarConHermano($query) {
        $query->whereHas('Inscripcion', function ($inscripciones) {
            return $inscripciones->where('hermano_id', '<>',null);
        });
    }
    function scopeFiltrarSinHermano($query) {
        $query->whereHas('Inscripcion', function ($inscripciones) {
            return $inscripciones->where('hermano_id',null);
        });
    }
    function scopeFiltrarSinEgreso($query) {
        $query->whereHas('Inscripcion', function ($inscripciones) {
            return $inscripciones->where('fecha_egreso',null);
        });
    }
    function scopeFiltrarConEgreso($query) {
        $query->whereHas('Inscripcion', function ($inscripciones) {
            return $inscripciones->where('fecha_egreso','<>',null);
        });
    }
    function scopeFiltrarEstadoInscripcion($query,$estado) {
        $query->whereHas('Inscripcion', function ($inscripciones) use($estado) {
            return $inscripciones->where('estado_inscripcion', $estado);
        });
    }
    function scopeFiltrarLegajo($query,$filtro) {
        $query->whereHas('Inscripcion', function ($q) use($filtro) {
            return $q->where('legajo_nro', $filtro);
        });
    }

    // Filtros de CENTRO
    function scopeFiltrarCentro($query,$centro_id)
    {
        $query->whereHas('Inscripcion.Centro', function ($centros) use($centro_id) {
            return $centros->where('id', $centro_id);
        });
    }
    function scopeFiltrarSector($query,$sector)
    {
        $query->whereHas('Inscripcion.Centro', function ($centro) use($sector) {
            return $centro->where('sector', $sector);
        });
    }
    function scopeFiltrarNivelServicio($query,$servicio)
    {
        $query->whereHas('Inscripcion.Centro', function ($centros) use($servicio) {
            return $centros->where('nivel_servicio', $servicio);
        });
    }
    function scopeFiltrarComunPrimario($query)
    {
        $query->filtrarNivelServicio('Común - Primario');
    }
    function scopeFiltrarComunSecundario($query)
    {
        $query->filtrarNivelServicio('Común - Secundario');
    }

    // Filtros de CICLO
    function scopeFiltrarCiclo($query,$ciclo_id) {
        $query->whereHas('Inscripcion.Ciclo', function ($ciclos) use($ciclo_id) {
            return $ciclos->where('id', $ciclo_id);
        });
    }
    function scopeFiltrarCicloNombre($query,$ciclo_nombre) {
        $query->whereHas('Inscripcion.Ciclo', function ($ciclos) use($ciclo_nombre) {
            return $ciclos->where('nombre', $ciclo_nombre);
        });
    }

    // Filtros de PERSONA
    function scopeFiltrarPersona($query,$persona_id) {
        $query->whereHas('Inscripcion.Alumno.Persona', function ($q) use($persona_id) {
            return $q->where('id', $persona_id);
        });
    }
    function scopeFiltrarPersonaCiudad($query,$ciudad) {
        $query->whereHas('Inscripcion.Alumno.Persona.Ciudad', function ($ciudades) use($ciudad) {
            return $ciudades->where('nombre', $ciudad);
        });
    }
}