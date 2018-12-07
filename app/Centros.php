<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Centros extends Model
{
    protected $table = 'centros';

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
}
