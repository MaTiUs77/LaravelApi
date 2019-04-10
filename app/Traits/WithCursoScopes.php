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

    function scopeFiltrarConDivision($query)
    {
        $query->whereHas('Curso', function ($cursos)  {
            return $cursos->where('division','<>', '');
        });
    }
    function scopeFiltrarCursoStatus($query, $status)
    {
        $query->whereHas('Curso', function ($cursos) use($status) {
            return $cursos->where('status', $status);
        });
    }

    //--- Permite Array ---
    function scopeFiltrarAnio($query,$param)
    {
        $query->whereHas('Curso', function ($q) use($param) {
            return $q->whereArr('anio', $param);
        });
    }
    function scopeFiltrarDivision($query,$param)
    {
        $query->whereHas('Curso', function ($q) use($param) {

            if($param=='vacia' || $param=='sin' || $param== 'null') {
                return $q->where('division','');
            } else if($param=='con'){
                return $q->where('division','<>','');
            } else {
                return $q->whereArr('division',$param);
            }
        });
    }
    //--- End Array ---
}
