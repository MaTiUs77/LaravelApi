<?php

namespace App;

use App\Traits\CustomPaginationScope;
use Illuminate\Database\Eloquent\Model;

class Inscripcions extends Model
{
    use CustomPaginationScope;

    protected $table = 'inscripcions';
    public $timestamps = false;
    protected $fillable = [
        'estado_inscripcion', 'legajo_nro'
    ];

    function Alumno()
    {
        return $this->hasOne('App\Alumnos', 'id', 'alumno_id');
    }

    function Hermano()
    {
        return $this->hasOne('App\Alumnos', 'id', 'hermano_id');
    }

    function Ciclo()
    {
        return $this->hasOne('App\Ciclos', 'id', 'ciclo_id');
    }

    function Centro()
    {
        return $this->hasOne('App\Centros', 'id', 'centro_id');
    }

    function Promocion()
    {
        return $this->belongsTo('App\Inscripcions', 'promocion_id', 'id')->with(['curso','centro']);
        //return $this->belongsTo('App\CursosInscripcions', 'promocion_id', 'inscripcion_id')->with(['curso']);
    }

    function Repitencia()
    {
        return $this->belongsTo('App\Inscripcions', 'repitencia_id', 'id')->with(['curso','centro']);
        //return $this->belongsTo('App\CursosInscripcions', 'repitencia_id', 'inscripcion_id')->with(['curso']);
    }

    function CursosInscripcions()
    {
        return $this->belongsTo('App\CursosInscripcions', 'id', 'inscripcion_id')->with('curso');
    }

    function Curso()
    {
        // USAR hasManyThrough con Laravel 5.8
        return $this->hasManyThrough(
            'App\Cursos',
            'App\CursosInscripcions',
            'inscripcion_id', // KEY CursosInscripcions
            'id',
            'id',
            'curso_id' // KEY CursosInscripcions
        );
    }

    function Pase()
    {
        return $this->hasOne('App\Centros', 'id', 'centro_origen_id');
    }
}
