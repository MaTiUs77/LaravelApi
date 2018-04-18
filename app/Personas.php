<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personas extends Model
{
    protected $table = 'personas';

    function Ciudad()
    {
        return $this->hasOne('App\Ciudades', 'id', 'ciudad_id');
    }

    function Alumnos()
    {
        return $this->hasMany('App\Alumnos', 'persona_id', 'id');
    }
}
