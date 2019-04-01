<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlumnosFamiliar extends Model
{
    protected $table = 'alumnos_familiars';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'alumno_id','familiar_id'
    ];
}
