<?php

namespace App\Http\Controllers\Api\Personas;

use App\Ciudades;
use App\Http\Controllers\Controller;
use App\Pases;
use App\Personas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class PersonasCrud extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.social');
    }

    // Index
    public function index(Request $req)
    {
        return 'index';
    }

    // Show
    public function show($id)
    {
        // Relacion de modelo
        $with = ['Ciudad'];

        if(Input::get('with')) {
            $appendWith = explode(',',Input::get('with'));
            $with = collect($with)->merge($appendWith)->unique()->toArray();
        }

        $query = Personas::with($with)->where('id',$id)->first();

        return $query;
    }

    // Add
    public function store()
    {
        $validationRules = [
            'vinculo' => 'required|string',
            'apellidos' => 'required|string',
            'nombres' => 'required|string',
            'sexo' => 'required|string',
            'documento_tipo' => 'required|string',
            'documento_nro' => 'required|numeric',
            'fecha_nac' => 'required|date',
            'email' => 'required|email',
            'telefono_nro' => 'required|numeric',
            'direccion' => 'required|string',
            'ciudad' => 'required|string',
            'observaciones' => 'string'
        ];

        // Se validan los parametros
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        // Verificar existencia
        $persona = Personas::where('email',Input::get('email'))->first();
        $ciudad = Ciudades::where('nombre',Input::get('ciudad'))->first();

        if(!$ciudad) { return ['error'=>'La ciudad es invalida']; }

        // Si no existe la persona... se crea!
        if(!$persona) {
            $persona = new Personas();
            $persona->apellidos = Input::get('apellidos');
            $persona->nombres= Input::get('nombres');
            $persona->sexo = Input::get('sexo');
            $persona->documento_tipo = Input::get('documento_tipo');
            $persona->documento_nro = Input::get('documento_nro');
            $persona->fecha_nac = Input::get('fecha_nac');
            $persona->email = Input::get('email');
            $persona->telefono_nro = Input::get('telefono_nro');
            $persona->calle_nombre= Input::get('direccion');
            $persona->ciudad_id= $ciudad->id;
            $persona->observaciones= Input::get('observaciones');

            // Por defecto el modo de la persona es familiar
            $persona->familiar = 1;

            // Sin default value
            $persona->pcia_nac='';
            $persona->nacionalidad='';

            // Elimina el atributo $append del modelo
            unset($persona['0']);

            $persona->save();
        }

        return compact('persona');
    }

    // Edit
    public function update($id)
    {
        $validationRules = [
            'cuenta' => 'required|string',
            'categoria' => 'required|string',
            'concepto' => 'required|string',
            'cuotas' => 'required|numeric',
            'cuota_nro' => 'required|numeric',
            'monto' => 'required|numeric',
            'fecha' => 'required|date',
        ];

        // Se validan los parametros
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }
    }

    // Delete
    public function destroy($id)
    {
        $validationRules = [
            'id' => 'required|numeric'
        ];

        // Se validan los parametros
        $validator = Validator::make(['id'=>$id], $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $query = Pases::find($id);

        if($query!=null)
        {
            $query->delete();

            return ['success'=>'Pase eliminado con exito'];
        }  else {
            return ['error'=>'El ID del pase no existe'];
        }
    }
}
