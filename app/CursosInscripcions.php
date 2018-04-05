<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CursosInscripcions extends Model
{
    protected $table = 'cursos_inscripcions';
    public $timestamps = false;

    // Por defecto se cargan estas relaciones
    protected $with = [
        'Curso',
        'Inscripcion.Ciclo',
        'Inscripcion.Centro.Ciudad',
        'Inscripcion.Alumno.Persona.Ciudad'
    ];

    function Curso()
    {
        return $this->hasOne('App\Cursos', 'id', 'curso_id');
    }
    function Inscripcion()
    {
        return $this->hasOne('App\Inscripcions', 'id', 'inscripcion_id');
    }

    // Filtros de INSCRIPCION
    function scopefiltrarConHermano($query) {
        $query->whereHas('Inscripcion', function ($inscripciones) {
            return $inscripciones->where('hermano_id', '<>',null);
        });
    }
    function scopefiltrarSinHermano($query) {
        $query->whereHas('Inscripcion', function ($inscripciones) {
            return $inscripciones->where('hermano_id',null);
        });
    }
    function scopefiltrarSinEgreso($query) {
        $query->whereHas('Inscripcion', function ($inscripciones) {
            return $inscripciones->where('fecha_egreso',null);
        });
    }
    function scopefiltrarConEgreso($query) {
        $query->whereHas('Inscripcion', function ($inscripciones) {
            return $inscripciones->where('fecha_egreso','<>',null);
        });
    }
    function scopefiltrarEstadoInscripcion($query,$estado) {
        $query->whereHas('Inscripcion', function ($inscripciones) use($estado) {
            return $inscripciones->where('estado_inscripcion', $estado);
        });
    }

    // Filtros de CURSO
    function scopeFiltrarTurno($query,$turno)
    {
        $query->whereHas('Curso', function ($cursos) use($turno) {
            return $cursos->where('turno', $turno);
        });
    }
    function scopeFiltrarAnio($query,$anio)
    {
        $query->whereHas('Curso', function ($cursos) use($anio) {
            return $cursos->where('anio', $anio);
        });
    }
    function scopeFiltrarDivision($query,$division)
    {
        $query->whereHas('Curso', function ($cursos) use($division) {
            return $cursos->where('division', $division);
        });
    }
    function scopeFiltrarConDivision($query)
    {
        $query->whereHas('Curso', function ($cursos)  {
            return $cursos->where('division','<>', '');
        });
    }

    // Filtros de CENTRO
    function scopeFiltrarNivelServicio($query,$servicio)
    {
        $query->whereHas('Inscripcion.Centro', function ($centros) use($servicio) {
            return $centros->where('nivel_servicio', $servicio);
        });
    }
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
    function scopeFiltrarComunPrimario($query)
    {
        $query->filtrarNivelServicio('Común - Primario');
    }
    function scopeFiltrarComunSecundario($query)
    {
        $query->filtrarNivelServicio('Común - Secundario');
    }

    // Filtros de CICLO
    function scopefiltrarCiclo($query,$ciclo_id) {
        $query->whereHas('Inscripcion.Ciclo', function ($ciclos) use($ciclo_id) {
            return $ciclos->where('id', $ciclo_id);
        });
    }

    function scopefiltrarCicloNombre($query,$ciclo_nombre) {
        $query->whereHas('Inscripcion.Ciclo', function ($ciclos) use($ciclo_nombre) {
            return $ciclos->where('nombre', $ciclo_nombre);
        });
    }

    // Filtros de PERSONA
    function scopefiltrarPersonaCiudad($query,$ciudad) {
        $query->whereHas('Inscripcion.Alumno.Persona.Ciudad', function ($ciudades) use($ciudad) {
            return $ciudades->where('nombre', $ciudad);
        });
    }
}
