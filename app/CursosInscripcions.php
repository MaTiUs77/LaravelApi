<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\WithCursoScopes;
use App\Traits\WithInscripcionScopes;
use Illuminate\Database\Eloquent\Model;

class CursosInscripcions extends Model
{
    use WithInscripcionScopes, WithCursoScopes, CustomPaginationScope;

    protected $table = 'cursos_inscripcions';
    public $timestamps = false;

    // Por defecto se cargan estas relaciones
    protected $with = [
        'Curso',
        'Inscripcion.Ciclo',
        'Inscripcion.Centro.Ciudad',
        'Inscripcion.Alumno.Persona.Ciudad'
    ];

    function Curso()
    {
        return $this->hasOne('App\Cursos', 'id', 'curso_id');
    }
    function Inscripcion()
    {
        return $this->hasOne('App\Inscripcions', 'id', 'inscripcion_id');
    }
}
