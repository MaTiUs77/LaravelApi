<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personas extends Model
{
    protected $table = 'personas';

    // Nombre completo dinamico
    protected $attributes = ['nombre_completo'];
    protected $appends = ['nombre_completo'];
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres}, {$this->apellidos}";
    }

    function Ciudad()
    {
        return $this->hasOne('App\Ciudades', 'id', 'ciudad_id');
    }

    function Alumnos()
    {
        return $this->hasMany('App\Alumnos', 'persona_id', 'id');
    }
}
