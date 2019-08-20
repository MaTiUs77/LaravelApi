<?php

namespace App\Http\Controllers\Api\Personas\v1;

use App\Ciudades;
use App\Http\Controllers\Api\Personas\v1\Request\PersonasCrudIndexReq;
use App\Http\Controllers\Api\Personas\v1\Request\PersonasCrudStoreReq;
use App\Http\Controllers\Api\Personas\v1\Request\PersonasCrudUpdateReq;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;
use App\Personas;
use App\Resources\PersonaTrayectoriaResource;
use App\UserSocial;
use Illuminate\Http\Request;
use App\Barrios;

class PersonasCrud extends Controller
{
    public function __construct(Request $req)
    {
        $this->middleware('jwt.social',['except'=>['index','show']]);
    }

    // List
    public function index(PersonasCrudIndexReq $req)
    {
        if(request('withFamiliar') !== null)
        {
            $persona = Personas::withOnDemand(['ciudad','barrio','familiar']);
        }
        else
        {
            $persona = Personas::withOnDemand(['ciudad','barrio']);
        }
        

        $persona->when(request('id'), function ($q, $v) {
            return $q->findOrFail($v);
        });

        $persona->when(request('documento_nro'), function ($q, $v) {
            return $q->where('documento_nro',$v);
        });

        $persona->when(request('nombres'), function ($q, $v) {
            return $q->where('nombres','like', "%$v%")
                ->orWhere('apellidos','like',"%$v%");
        });

        // Request toma el valor 0 (cero) como falso, lo que impide filtrar alumno=0
        if(request('alumno') != null){
            $persona->where('alumno',request('alumno'));
        }
        if(request('familiar') != null){
            $persona->where('familiar',request('familiar'));
        }

        $result = $persona->customPagination();

        switch(request('render')) {
            case 'trayectoria':
                return new PersonaTrayectoriaResource($result);
            break;
        }

        return $result;
    }

    // View
    public function show($id)
    {
        // Se validan los parametros
        $input = ['id'=>$id];
        $rules = ['id'=>'numeric'];

        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        // Continua si las validaciones son efectuadas
        $persona = Personas::withOnDemand(['ciudad']);

        // Obtiene todas las inscripciones filtradas por un ciclo_id especifico
        $persona->when(request('ciclo_id'), function ($q, $param) {
            $q->whereHas('alumnos.inscripciones',$filter = function($q) use($param) {
                return $q->where('ciclo_id',$param);
            })->with(['alumnos.inscripciones'=>$filter]);
        });

        $result = $persona->findOrFail($id);

        switch(request('render')) {
            case 'trayectoria':
                PersonaTrayectoriaResource::withoutWrapping();
                return new PersonaTrayectoriaResource($result);
                break;
            default:
                return $result;
                break;
        }
    }

    // Create
    public function store(PersonasCrudStoreReq $req)
    {
        $ciudad = Ciudades::where('nombre',request('ciudad'))->first();
        $barrio = Barrios::where('nombre','like','%'.request('barrio').'%')->first();

        // Verificar existencia de la persona, segun DNI
        $persona = Personas::where('documento_nro',request('documento_nro'))->first();

        // Si no existe la persona... se crea!
        if(!$persona) {
            // Se agrega el campo ciudad_id al request
            $req->merge(["ciudad_id"=>$ciudad->id,"barrio_id"=>$barrio->id]);
            
            // Se crea la persona
            $persona = Personas::create($req->except("vinculo"));
        }

        if($persona != null && $persona->familiar) {
            $this->updatePersonaIdFromUserSocial($persona->id);
        }

        return compact('persona');
    }

    // Update
    public function update($id,PersonasCrudUpdateReq $req)
    {
        // Verificar existencia de la persona, segun DNI
        $persona = Personas::findOrFail($id);

        // Si existe la persona... se actualiza!
        if($persona) {
            // Obtenemos los datos del Usuario Social que consume el API
            $jwt_user = (object) request('jwt_user');
            if($jwt_user->id) {

                if ($persona->id == $jwt_user->persona_id) {
                    // Se agrega el campo ciudad_id al request
                    $realReq = collect(request()->except(['jwt_user','vinculo']));

                    if(request('ciudad'))  {
                        $ciudad = Ciudades::where('nombre',request('ciudad'))->first();
                        $barrio = Barrios::where('nombre','like','%'.request('barrio').'%')->first();
                        $realReq = $realReq->merge(["ciudad_id"=>$ciudad->id,"barrio_id"=>$barrio->id]);
                    }

                    // Se crea la persona
                    $updated = $persona->update($realReq->toArray());

                    return ['updated'=>$updated];
                } else {
                    return ['error'=>'La persona que intenta editar, no corresponde a su perfil'];
                }
            }
        }

        return compact('persona');
    }

    private function updatePersonaIdFromUserSocial($persona_id) {
        // la variable jwt_user es enviada por el middleware, luego de verificar el token
        $jwt_user = (object) request('jwt_user');
        if($jwt_user->id)
        {
            $socialUser = UserSocial::where('id',$jwt_user->id)->first();
            $socialUser->persona_id = $persona_id;
            $socialUser->save();
        }
    }
}