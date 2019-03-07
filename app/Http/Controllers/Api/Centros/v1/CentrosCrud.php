<?php

namespace App\Http\Controllers\Api\Centros\v1;

use App\Centros;
use App\Ciudades;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Api\Utilities\WithOnDemand;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

class CentrosCrud extends Controller
{
    public function __construct(Request $req)
    {
    }

    public function index()
    {
        // Se validan los parametros
        $input = request()->all();
        $rules = [
            'nivel_servicio' => 'string',
            'ciudad' => 'string',
            'ciudad_id' => 'numeric',
            'sector' => 'string',
            'nombre' => 'string'
        ];
        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        $query = Centros::with(['ciudad']);

        $query->when(request('ciudad_id'), function ($q, $v) {
            return $q->where('ciudad_id', $v);
        });

        $query->when(request('ciudad'), function ($q, $v) {
            $ciudad = Ciudades::where('nombre',$v)->firstOrFail();
            return $q->where('ciudad_id', $ciudad->id);
        });

        $query->when(request('sector'), function ($q, $v) {
            return $q->where('sector', $v);
        });

        $query->when(request('nivel_servicio'), function ($q, $v) {
            return $q->where('nivel_servicio', $v);
        });

        $query->when(request('nombre'), function ($q, $v) {
            return $q->where('nombre','like',"%$v%");
        });

       $query->when(request('division'), function ($q, $v) {
           $q->byCursosDivision($v);
        });

        $centro = $query->get();

        if($centro->isNotEmpty()) {
            return $centro;
        } else {
            abort(204,'No se encontraron resultados con el filtro aplicado');
        }
    }

    public function show($id)
    {
        // Se validan los parametros
        $input = ['id'=>$id];
        $rules = ['id'=>'required|numeric'];
        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        $query = Centros::with(['ciudad']);

        $query->when(request('division'), function ($q, $v) {
            $q->byCursosDivision($v);
        });
        
        // Localiza el centro en cuestion
        $centro = $query->findOrFail($id);
        
        return $centro;
    }
}
