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
            'sector' => 'string',
            'centro_id' => 'numeric'
        ];
        if($fail = DefaultValidator::make($input,$rules)) return $fail;
        
        $query = Cursos::withOnDemand();

        $query->when(request('sector'), function ($q, $v) {
            return $q->where('sector', $v);
        });

        $query->when(request('centro_id'), function ($q, $v) {
            return $q->whereHas('centro', function($subq) use($v) {
                $subq->where('id',$v);
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
