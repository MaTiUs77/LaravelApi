<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CursosInscripcions extends Model
{
    protected $table = 'cursos_inscripcions';

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

    // Metodo magico para ORM
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

    function scopeFiltrarConDivision($query)
    {
        $query->whereHas('Curso', function ($cursos)  {
            return $cursos->where('division','<>', '');
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

    function scopefiltrarCiclo($query,$ciclo_id) {
        $query->whereHas('Inscripcion.Ciclo', function ($ciclos) use($ciclo_id) {
            return $ciclos->where('id', $ciclo_id);
        });
    }

    function scopefiltrarPersonaCiudad($query,$ciudad) {
        $query->whereHas('Inscripcion.Alumno.Persona.Ciudad', function ($ciudades) use($ciudad) {
            return $ciudades->where('nombre', $ciudad);
        });
    }

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

    function scopefiltrarEstadoInscripcion($query,$estado) {
        $query->whereHas('Inscripcion', function ($inscripciones) use($estado) {
            return $inscripciones->where('estado_inscripcion', $estado);
        });
    }
}
