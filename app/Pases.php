<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\WithCicloScopes;
use Illuminate\Database\Eloquent\Model;

class Pases extends Model
{
    use WithCicloScopes, CustomPaginationScope;

    protected $table = 'pases';

    function Usuario()
    {
        return $this->hasOne('App\User', 'id', 'usuario_id');
    }

    function Alumno()
    {
        return $this->hasOne('App\Alumnos', 'id', 'alumno_id');
    }

    function Ciclo()
    {
        return $this->hasOne('App\Ciclos', 'id', 'ciclo_id');
    }

    // Nueva version
    function Origen()
    {
        return $this->hasOne('App\Centros', 'id', 'centro_id');
    }

    function Destino()
    {
        return $this->hasOne('App\Centros', 'id', 'centro_id_destino');
    }

    // DEPRECAR
    function CentroOrigen()
    {
        return $this->hasOne('App\Centros', 'id', 'centro_id_origen');
    }

    function CentroDestino()
    {
        return $this->hasOne('App\Centros', 'id', 'centro_id_destino');
    }

    // WITH SCOPES
    function scopeFiltrarCentroOrigen($query,$centro_origen)
    {
        $query->whereHas('CentroOrigen', function ($centros) use($centro_origen) {
            return $centros->where('id', $centro_origen);
        });
    }

    function scopeFiltrarCentroDestino($query,$centro_destino)
    {
        $query->whereHas('CentroDestino', function ($centros) use($centro_destino) {
            return $centros->where('id', $centro_destino);
        });
    }
}
