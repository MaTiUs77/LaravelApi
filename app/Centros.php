<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;

class Centros extends Model
{
    protected $table = 'centros';

    protected $with=[
        'Cursos'
    ];

    function Barrio()
    {
        return $this->hasOne('App\Barrios', 'id', 'barrio_id');
    }

    function Ciudad()
    {
        return $this->hasOne('App\Ciudades', 'id', 'ciudad_id');
    }

    function Departamentos()
    {
        return $this->hasOne('App\Departamentos', 'id', 'departamento_id');
    }

    function Cursos()
    {
        $division = Input::get('division');

        $curso = $this->hasOne('App\Cursos', 'centro_id', 'id');

        if($division){
            $curso->where('division','<>','');
        }
        return $curso;
    }
}
