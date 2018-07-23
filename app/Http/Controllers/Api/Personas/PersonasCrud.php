<?php

namespace App\Http\Controllers\Api\Personas;

use App\Ciudades;
use App\Http\Controllers\Controller;
use App\Personas;
use App\UserSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class PersonasCrud extends Controller
{
    public function __construct(Request $req)
    {
        $this->middleware('jwt.social',['except'=>['index']]);

    }

    public function index()
    {
        $authApi = env('SIEP_AUTH_API');
        $laravelApi = env('SIEP_LARAVEL_API');
        return compact('authApi','laravelApi');
    }

    // Create
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

        $ciudad = Ciudades::where('nombre',Input::get('ciudad'))->first();

        // Verificar existencia de la persona, segun EMAIL o DNI
        $persona = Personas::where('email',Input::get('email'))
            ->orWhere('documento_nro',Input::get('documento_nro'))
            ->first();

        if(!$ciudad) { return ['error'=>'La ciudad es invalida']; }

        // Si no existe la persona... se crea!
        if(!$persona) {
            $persona = new Personas();
            $persona->apellidos = strtoupper(Input::get('apellidos'));
            $persona->nombres= strtoupper(Input::get('nombres'));
            $persona->sexo = strtoupper(Input::get('sexo'));
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

            // Campos sin default value en la db
            $persona->pcia_nac='';
            $persona->nacionalidad='';

            // Elimina el atributo $append del modelo persona
            unset($persona['0']);

            $persona->save();

            // Update UserSocial persona_id
            $jwt_user = (object) request()->get('jwt_user');
            if($jwt_user->id)
            {
                $socialUser = UserSocial::where('id',$jwt_user->id)->first();
                $socialUser->persona_id = $persona->id;
                $socialUser->save();
            }
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
}
