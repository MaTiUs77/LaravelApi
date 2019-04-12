<?php

namespace App\Http\Controllers\Api\Cursos\v1;

use App\Cursos;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;

class CursosCrud extends Controller
{
    public function index()
    {
        // Se validan los parametros
        $input = request()->all();
        $rules = [
            'tipo' => 'string',
            'anio' => 'string',
            'division' => 'string',
            'turno' => 'string',
            'centro_id' => 'numeric',
            'ciudad' => 'string',
        ];
        if($fail = DefaultValidator::make($input,$rules)) return $fail;
        
        $query = Cursos::withOnDemand();

        // Por defecto se muestran los cursos con el flag
        // status = 1
        $query->where('status',1);

        $query->when(request('tipo'), function ($q, $v) {
            return $q->where('tipo', $v);
        });

        $query->when(request('anio'), function ($q, $v) {
            return $q->where('anio', $v);
        });

        $query->when(request('division'), function ($q, $v) {
            return $q->where('division', $v);
        });

        $query->when(request('turno'), function ($q, $v) {
            return $q->where('turno', $v);
        });

        // Filtros de centros
        $query->when(request('centro_id'), function ($q, $v) {
            return $q->whereHas('centro', function($subq) use($v) {
                $subq->where('id',$v);
            });
        });

        $query->when(request('ciudad'), function ($q, $v) {
            return $q->whereHas('centro.ciudad', function($subq) use($v) {
                $subq->where('nombre',$v);
            });
        });

        $result = $query->customPagination();
        return $result;
    }

    public function show($id)
    {
        // Se validan los parametros
        $input = ['id'=>$id];
        $rules = ['id'=>'required|numeric'];
        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        $query = Cursos::withOnDemand();
        return $query->findOrFail($id);
    }
}
