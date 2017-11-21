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
}
