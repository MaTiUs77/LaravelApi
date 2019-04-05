<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\Scopes\CursosManyScopes;
use App\Traits\Scopes\CursosScopes;
use App\Traits\Scopes\ManyCursosScopes;
use App\Traits\WithOnDemandTrait;
use Illuminate\Database\Eloquent\Model;

class Centros extends Model
{
    use ManyCursosScopes, CustomPaginationScope, WithOnDemandTrait;

    protected $table = 'centros';

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float'
    ];

    function Barrio()
    {
        return $this->hasOne('App\Barrios', 'id', 'barrio_id');
    }

    function Ciudad()
    {
        return $this->hasOne('App\Ciudades', 'id', 'ciudad_id');
    }

    function Departamento()
    {
        return $this->hasOne('App\Departamentos', 'id', 'departamento_id');
    }

    function Cursos()
    {
        return $this->hasMany('App\Cursos', 'centro_id', 'id');
    }

    function Titulaciones()
    {
        return $this->hasMany('App\CentrosTitulacions', 'centro_id', 'id');
    }

    function NivelServicio()
    {
        return $this->hasOne('App\NivelServicio', 'nombre', 'nivel_servicio');
    }
}


