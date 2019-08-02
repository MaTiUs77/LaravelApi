<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Pagination\LengthAwarePaginator;

class InscripcionFindResource extends Resource
{
    public function toArray($request)
    {
        if($this->resource instanceof LengthAwarePaginator)
        {
            // Render de una paginacion
            $pagination = [
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'per_page' => $this->resource->perPage(),
                'total' => $this->resource->total(),
            ];

            return [
                'meta' => $pagination,
                'data' => $this->resource->map(function($item){
                    return $this->render($item);
                }),
            ];

        } else {
            // Render de un unico resultado
            return $this->render($this);
        }
    }

    public function render($item) {
        $first = $item->first();
        $inscripcion = $first->inscripcion;

        $cursos = $item->map(function($v) {
            return $v->curso;
        });

        return compact('inscripcion','cursos');
    }
}