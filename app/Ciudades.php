<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\WithOnDemandTrait;
use Illuminate\Database\Eloquent\Model;

class Ciudades extends Model
{
    use WithOnDemandTrait, CustomPaginationScope;
    
    protected $table = 'ciudads';

    function Departamento()
    {
        return $this->hasOne('App\Departamentos', 'id', 'departamento_id');
    }
}
