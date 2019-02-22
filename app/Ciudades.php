<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ciudades extends Model
{
    protected $table = 'ciudads';

    function Departamento()
    {
        return $this->hasOne('App\Departamentos', 'id', 'departamento_id');
    }
}
