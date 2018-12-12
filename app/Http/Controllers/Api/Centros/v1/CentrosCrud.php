<?php

namespace App\Http\Controllers\Api\Centros\v1;

use App\Centros;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class CentrosCrud extends Controller
{
    public function __construct(Request $req)
    {
    }

    // View
    public function show($id)
    {
        $validationRules = [
            'id' => 'required|numeric'
        ];

        $inputs = Input::all();
        $inputs['id'] = $id;

        // Se validan los parametros
        $validator = Validator::make($inputs, $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        // Relacion por defecto
        $with = ['Ciudad','Barrio'];
        // Relacion adicional por medio de urlquery
        if(Input::get('with')) {
            $appendWith = explode(',',Input::get('with'));
            $with = collect($with)->merge($appendWith)->unique()->toArray();
        }

        // Localiza el centro en cuestion
        $query = Centros::with($with);
        $centro = $query->find($id);

        if($centro) {
            return $centro;
        } else {
            abort(400,'No se encontro un centro con esa ID');

            return compact('error');
        }
    }
}
