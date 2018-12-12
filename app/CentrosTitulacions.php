<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CentrosTitulacions extends Model
{
    protected $table = 'centros_titulacions';
    public $timestamps = false;

    function Titulacion()
    {
        return $this->hasOne('App\Titulacion', 'id', 'titulacion_id');
    }
}
