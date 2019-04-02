<?php

namespace App\Traits\Scopes;

trait ManyCursosScopes {

    private $modelName = 'cursos';

    function scopeManyCursosDivision($query,$division)
    {
        return $query->whereHas('cursos', function ($cursos) use($division)
        {
            if($division=='vacia' || $division=='sin' || $division == 'null') {
                $cursos->where('division','');
            } else if($division=='con'){
                $cursos->where('division','<>','');
            } else {
                $cursos->where('division',$division);
            }

            return $cursos;
        })
            ->with(['cursos' => function ($cursos)  use($division) {

                if($division=='vacia' || $division=='sin' || $division == 'null') {
                    $cursos->where('division','');
                } else if($division=='con'){
                    $cursos->where('division','<>','');
                } else {
                    $cursos->where('division',$division);
                }

                return $cursos->orderBy('anio', 'asc');
            }]);
    }

    function scopeManyCursosAnio($query,$anio)
    {
        return $query->whereHas('cursos', function ($cursos) use($anio)
        {
            $cursos->where('anio',$anio);
            return $cursos;
        })
            ->with(['cursos' => function ($cursos) use($anio) {
                return $cursos->where('anio',$anio);
            }]);
    }
/*    function scopeFiltrarTurno($query,$turno)
    {
        $query->whereHas('Cursos', function ($cursos) use($turno) {
            return $cursos->where('turno', $turno);
        });
    }
    function scopeFiltrarAnio($query,$anio)
    {
        $query->whereHas('Cursos', function ($cursos) use($anio) {
            return $cursos->where('anio', $anio);
        });
    }
    function scopeFiltrarDivision($query,$division)
    {
        $query->whereHas('Cursos', function ($cursos) use($division) {

            if($division=='vacia' || $division=='sin' || $division == 'null') {
                return $cursos->where('division','');
            } else if($division=='con'){
                return $cursos->where('division','<>','');
            } else {
                return $cursos->where('division',$division);
            }
        });
    }
    function scopeFiltrarConDivision($query)
    {
        $query->whereHas('Cursos', function ($cursos)  {
            return $cursos->where('division','<>', '');
        });
    }*/
}
