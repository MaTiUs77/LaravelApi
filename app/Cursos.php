<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cursos extends Model
{
    protected $table = 'cursos';

    public $timestamps = false;

    function Centro()
    {
        return $this->hasOne('App\Centros', 'id', 'centro_id');
    }

    function Titulacion()
    {
        return $this->hasOne('App\Titulacion', 'id', 'titulacion_id');
    }

}
