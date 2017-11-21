<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CursosInscripcions extends Model
{
    protected $table = 'cursos_inscripcions';

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
        $query->whereHas('Curso.Centro', function ($centros) use($servicio) {
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

    function scopefiltrarCiclo($query,$anio) {
        $query->whereHas('Inscripcion.Ciclo', function ($ciclos) use($anio) {
            return $ciclos->where('nombre', $anio);
        });
    }

    function scopefiltrarCiudad($query,$ciudad) {
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
}
