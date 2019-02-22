<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Centros extends Model
{
    protected $table = 'centros';

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float'
    ];

    function Barrio()
    {
        return $this->hasOne('App\Barrios', 'id', 'barrio_id');
    }

    function Ciudad()
    {
        return $this->hasOne('App\Ciudades', 'id', 'ciudad_id');
    }

    function Departamento()
    {
        return $this->hasOne('App\Departamentos', 'id', 'departamento_id');
    }

    function Cursos()
    {
        return $this->hasMany('App\Cursos', 'centro_id', 'id');
    }

    function Titulaciones()
    {
        return $this->hasMany('App\CentrosTitulacions', 'centro_id', 'id');
    }

    function NivelServicio()
    {
        return $this->hasOne('App\NivelServicio', 'nombre', 'nivel_servicio');
    }

    public function scopefiltrarCursos($query, $division)
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

                return $cursos->orderBy('id', 'desc');
            }]);
    }
}


