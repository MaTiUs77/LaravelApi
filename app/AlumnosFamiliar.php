<?php

namespace App;

use App\Traits\CustomPaginationScope;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithOnDemandTrait;

class AlumnosFamiliar extends Model
{
    use WithOnDemandTrait, CustomPaginationScope;
    
    protected $table = 'alumnos_familiars';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'alumno_id','familiar_id','status'
    ];

    function Alumno()
    {
        return $this->belongsTo('App\Alumnos', 'alumno_id', 'id');
    }

    function Familiar()
    {
        return $this->belongsTo('App\Familiar', 'familiar_id', 'id');
    }
}
