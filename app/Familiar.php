<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Familiar extends Model
{
    protected $table = 'familiars';

    function Persona()
    {
        return $this->hasOne('App\Personas', 'id', 'persona_id');
    }
}
