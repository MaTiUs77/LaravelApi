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
        'ciclo_id',
        'inscripcion_id',
        'centro_id',

        'centro_id_destino_a',
        'centro_id_destino_b',
        'anio',

        'nota_pase_tutor',
        'tipo',
        'motivo',
        'observaciones',
        'observaciones',
        'user_id',
        'familiar_id',
        'fecha_vencimiento'
    ];
}
