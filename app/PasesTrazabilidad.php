<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasesTrazabilidad extends Model
{
    protected $table = 'pases_trazabilidad';
    protected $primaryKey = 'trazabilidad_id';

    // Permite usar Model::create(array[])
    protected $fillable= [
        'id',
        'inscripcion_id_origen',
        'centro_id_destino',
        'anio',
        'user_id'
    ];
}
