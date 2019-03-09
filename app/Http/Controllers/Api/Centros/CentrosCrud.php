<?php

namespace App\Http\Controllers\Api\Centros;

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
            'id' => 'required|numeric',
        ];

        $inputs = Input::all();
        $inputs['id'] = $id;

        // Se validan los parametros
        $validator = Validator::make($inputs, $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $centros = Centros::with(['Ciudad','Barrio']);
        $centro = $centros->find($id);

        if($centro) {
            return $centro;
        } else {
            $error = [
                'message' => 'No se encontro un centro con esa ID'
            ];

            return compact('error');
        }

    }
}
