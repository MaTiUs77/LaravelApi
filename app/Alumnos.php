<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\WithOnDemandTrait;
use Illuminate\Database\Eloquent\Model;

class Alumnos extends Model
{
    use WithOnDemandTrait, CustomPaginationScope;

    protected $table = 'alumnos';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id','persona_id','centro_id','legajo_fisico_nro','pendiente'
    ];

    function Familiares()
    {
        return $this->hasMany('App\AlumnosFamiliar', 'alumno_id', 'id');
    }

/*    function Familiares()
    {
        return $this->hasManyThrough(
            'App\Familiar',
            'App\AlumnosFamiliar',
            'alumno_id', // Foreign key on users table...
            'id', // Foreign key on posts table...
            'id', // Local key on countries table...
            'familiar_id' // Local key on users table...
        );
            //->where('status','pendiente');
    }*/

    function Persona()
    {
        return $this->belongsTo('App\Personas', 'persona_id', 'id');
    }

    function Centro()
    {
        return $this->belongsTo('App\Centros', 'centro_id','id');
    }

    function Inscripciones()
    {
        return $this->hasMany('App\Inscripcions', 'alumno_id','id');
    }

    function UltimaInscripcion()
    {
        return $this->hasOne('App\Inscripcions', 'alumno_id','id')->orderBy('ciclo_id','desc');
    }
}
