<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\WithCursoScopes;
use App\Traits\WithOnDemandTrait;
use Illuminate\Database\Eloquent\Model;

class Cursos extends Model
{
    use WithOnDemandTrait, CustomPaginationScope;

    protected $table = 'cursos';

    public $timestamps = false;

    function Centro()
    {
        return $this->belongsToMany('App\Centros', 'id', 'centro_id');
    }

    function Titulacion()
    {
        return $this->hasOne('App\Titulacion', 'id', 'titulacion_id');
    }

}
