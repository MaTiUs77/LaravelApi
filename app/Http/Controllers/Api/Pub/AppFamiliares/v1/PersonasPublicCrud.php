<?php

namespace App\Http\Controllers\Api\Pub\AppFamiliares\v1;

use App\Familiar;
use App\Http\Controllers\Api\Utilities\ApiConsume;

use App\Http\Controllers\Controller;
use App\Resources\PersonaPublicResource;
use Illuminate\Support\Facades\Input;
use App\Resources\PersonaPublicResource_01;
use App\Resources\PersonaPublicResource_02;
use App\Http\Controllers\Api\Personas\v1\Request\PersonasCrudStoreReq;
use App\Http\Controllers\Api\Personas\v1\Request\PersonasCrudUpdateReq;
use App\Personas;
use App\UserSocial;
use Illuminate\Http\Request;

class PersonasPublicCrud extends Controller
{
    public function __construct(Request $req)
    {
        $this->middleware('jwt.social',['except'=>['index','show']]);
    }

    public function index() {
        $params = Input::all();
        $api = new ApiConsume();
        $api->get("personas",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        return $response;
        
        $data = collect($response['data']);

        if(isset($params["documento_nro"]))
        {
            return PersonaPublicResource_02::collection($data);
        }
        // else{
        //     return PersonaPublicResource_01::collection($data);
        // }
    }

    public function show($id) {
        $params = Input::all();
        $api = new ApiConsume();
        $api->get("personas/{$id}",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        // El resource requiere que la informacion sea enviada dentro de la variable $data
        $data = $response;
        $data = compact('data');
        $data = collect($data);
        return PersonaPublicResource::collection($data);
    }

    public function store(PersonasCrudStoreReq $req)
    {
        $api = new ApiConsume();

        $api->get("ciudades",["nombre"=>$req["ciudad"]]);
        if($api->hasError()) { return $api->getError(); }
        $ciudad = $api->response();
        $vinculo = $req['vinculo'];
        // $ciudad = $ciudad["data"];
        
        $api->get("barrios",["nombre"=>$req["barrio"]]);
        if($api->hasError()) { return $api->getError(); }
        $barrio = $api->response();
        // $barrio = $barrio["data"];

        // Verificar existencia de la persona, segun DNI
        // $persona = Personas::where('documento_nro',request('documento_nro'))->first();
        $api->get("personas",["documento_nro"=>$req["documento_nro"]]);
        if($api->hasError()) { return $api->getError(); }
        $persona = $api->response();
        $persona = collect($persona["data"]);
        // Si no existe la persona... se crea!
        if($persona->isEmpty()) {
            // Se agrega el campo ciudad_id y barrio_id al request
            $req->merge(["ciudad_id"=>$ciudad[0]["id"],"barrio_id"=>$barrio[0]["id"]]);
            // Se crea la persona
            $persona = Personas::create($req->except("vinculo"));
        }
        else {
            $persona = (object) $persona[0];
        }

        if($persona != null && $persona->familiar) {
            $jwt_user = (object) request('jwt_user');
            if(isset($jwt_user->id))
            {
                $socialUser = UserSocial::where('id',$jwt_user->id)->first();
                $socialUser->persona_id = $persona->id;
                $socialUser->save();
            }
            else{
                return "No posee jwt";
            }

            $familiar = Familiar::where('persona_id',$persona->id)->first();
            if(!$familiar){
                $familiarReq = [
                    "persona_id" => $persona->id,
                    "vinculo"=> $vinculo,
                    "conviviente"=> 1,
                    "autorizado_retirar"=>0,
                    "observaciones"=>""
                ];
                $familiar = Familiar::create($familiarReq);
            }else{
                
            }
            $this->updatePersonaIdFromUserSocial($persona->id);
            // self::updatePersonaIdFromUserSocial($persona->id);
        }

        return compact('persona');
    }

    // Update
    public function update($id,PersonasCrudUpdateReq $req)
    {
        $api = new ApiConsume();
        // Verificar existencia de la persona, segun DNI
        // $persona = Personas::findOrFail($id);
        $api->get("personas",["id"=>$id]);
        if($api->hasError()) { return $api->getError(); }
        $persona = $api->response();
        $persona = collect($persona['data']);

        // Si existe la persona... se actualiza!
        if($persona) {
            $persona = (object) $persona[0];
            // Obtenemos los datos del Usuario Social que consume el API
            $jwt_user = (object) request('jwt_user');
            $vinculo = request('vinculo');
            if($jwt_user->id) {

                if ($persona->id == $jwt_user->persona_id) {
                    // Se agrega el campo ciudad_id al request
                    $realReq = collect(request()->except(['jwt_user','vinculo']));

                    if(request('ciudad'))  {
                        // $ciudad = Ciudades::where('nombre',request('ciudad'))->first();
                        // $barrio = Barrios::where('nombre','like','%'.request('barrio').'%')->first();

                        $api->get("ciudades",["nombre"=>$req["ciudad"]]);
                        if($api->hasError()) { return $api->getError(); }
                        $ciudad = $api->response();
                        $ciudad = $ciudad[0];
                        
                        $api->get("barrios",["nombre"=>$req["barrio"]]);
                        if($api->hasError()) { return $api->getError(); }
                        $barrio = $api->response();
                        $barrio = $barrio[0];

                        $realReq = $realReq->merge(["ciudad_id"=>$ciudad["id"],"barrio_id"=>$barrio["id"]]);
                    }
                    // Se actualiza la persona
                    $personas = Personas::findOrFail($id);
                    // Busco familiar y lo actualizo
                    $familiar = Familiar::where('persona_id',$jwt_user->persona_id)->first();
                    if($familiar){
                        $familiar->update(["vinculo"=>$vinculo]);
                    }else{
                        
                    }
                    $updated = $personas->update($realReq->toArray());

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
        else{
            return "No posee jwt";
        }
    }
}