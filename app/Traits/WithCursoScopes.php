<?php

namespace App\Traits;

trait WithCursoScopes {

    function scopeFiltrarCurso($query,$filtro)
    {
        $query->whereHas('Curso', function ($q) use($filtro) {
            return $q->where('curso_id', $filtro);
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
}
