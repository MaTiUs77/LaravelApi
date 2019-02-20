<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alumnos extends Model
{
    protected $table = 'alumnos';

    function Persona()
    {
        return $this->hasOne('App\Personas', 'id', 'persona_id');
    }

    function Centro()
    {
        return $this->hasOne('App\Centros', 'id', 'centro_id');
    }

    function Inscripcion()
    {
        return $this->belongsToMany('App\Inscripcions', 'alumno_id', 'id');
    }
}
