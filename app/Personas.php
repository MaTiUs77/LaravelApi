<?php

namespace App;

use App\Traits\CustomPaginationScope;
use Illuminate\Database\Eloquent\Model;

class Personas extends Model
{
    use CustomPaginationScope;
    
    protected $table = 'personas';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'apellidos','nombres','sexo','documento_tipo','documento_nro',
        'fecha_nac','email','ciudad_id','telefono_nro','calle_nombre','calle_nro',
        'depto_casa','tira_edificio','observaciones','familiar','alumno',

        'pcia_nac','nacionalidad'
    ];

    // Nombre completo dinamico
//    protected $attributes = ['nombre_completo'];
    protected $appends = ['nombre_completo'];

    public function getNombreCompletoAttribute()
    {
        return "{$this->apellidos}, {$this->nombres}";
    }

    function Ciudad()
    {
        return $this->hasOne('App\Ciudades', 'id', 'ciudad_id');
    }

    function Alumnos()
    {
        return $this->hasMany('App\Alumnos', 'persona_id', 'id');
    }

    function Barrio()
    {
        return $this->hasOne('App\Barrios', 'id', 'barrio_id');
    }
}
