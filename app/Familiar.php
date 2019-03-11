<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Familiar extends Model
{
    protected $table = 'familiars';

    function Persona()
    {
        return $this->belongsTo('App\Personas', 'persona_id', 'id');
    }
}
