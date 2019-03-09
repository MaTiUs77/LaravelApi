<?php

namespace App\Traits;

trait WithCicloScopes {

    function scopeFiltrarCiclo($query,$ciclo_id) {
        $query->whereHas('Ciclo', function ($ciclos) use($ciclo_id) {
            return $ciclos->where('id', $ciclo_id);
        });
    }
    function scopeFiltrarCicloNombre($query,$ciclo_nombre) {
        $query->whereHas('Ciclo', function ($ciclos) use($ciclo_nombre) {
            return $ciclos->where('nombre', $ciclo_nombre);
        });
    }
}
