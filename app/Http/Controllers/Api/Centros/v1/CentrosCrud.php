<?php

namespace App\Http\Controllers\Api\Centros\v1;

use App\Centros;
use App\Ciudades;
use App\Http\Controllers\Api\Utilities\WithOnDemand;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

class CentrosCrud extends Controller
{
    public function __construct(Request $req)
    {
    }

    public function index()
    {
        $validationRules = [
            'nivel_servicio' => 'string',
            'ciudad' => 'string',
            'ciudad_id' => 'numeric',
            'sector' => 'string',
            'nombre' => 'string'
        ];

        // Se validan los parametros
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            return [
                'error' => 'Parametros invalidos',
                'parametros' => $validator->errors()
            ];
        }

        // Adjunta foregin keys on demand
        $with = WithOnDemand::set(['Ciudad'], Input::get('with'));

        // Opciones de UrlQuery
        $nivel_servicio = Input::get('nivel_servicio');
        $ciudad = Input::get('ciudad');
        $ciudad_id = Input::get('ciudad_id');
        $sector = Input::get('sector');
        $nombre = Input::get('nombre');

        $query = Centros::with($with);

        if($nivel_servicio) {
            $query->where('nivel_servicio',$nivel_servicio);
        }

        if($ciudad_id) {
            $query->where('ciudad_id',$ciudad_id);
        }

        if($ciudad) {
            $ciudad = Ciudades::where('nombre',$ciudad)->first();
            if($ciudad!=null) {
                $query->where('ciudad_id',$ciudad->id);
            } else {
                abort(400,'La ciudad solicitada no existe');
            }
        }

        if($sector) {
            $query->where('sector',$sector);
        }

        if($nombre) {
            $query->where('nombre','like','%'.$nombre.'%');
        }


        $centro = $query->get();

        if($centro !=null || count($centro)<=0) {
            return $centro;
        } else {
            abort(400,'No se encontraron centros con el filtro aplicado');

            return compact('error');
        }
    }

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

        // Adjunta foregin keys on demand
        $with = WithOnDemand::set(['Ciudad'], Input::get('with'));

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
