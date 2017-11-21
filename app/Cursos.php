<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cursos extends Model
{
    protected $table = 'cursos';

    function Centro()
    {
        return $this->hasOne('App\Centros', 'id', 'centro_id');
    }
}
