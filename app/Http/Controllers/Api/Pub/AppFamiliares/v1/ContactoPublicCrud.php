<?php

namespace App\Http\Controllers\Api\Pub\AppFamiliares\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;

use App\Http\Controllers\Controller;
use App\Resources\PersonaPublicResource;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Api\Contacto\v1\Request\ContactoCrudStoreReq;
use App\Http\Controllers\Api\Personas\v1\Request\PersonasCrudUpdateReq;
use App\Resources\ContactoPublicResource_01;
use App\Contacto;
use App\UserSocial;
use Illuminate\Http\Request;

class ContactoPublicCrud extends Controller
{
    public function __construct(Request $req)
    {
        $this->middleware('jwt.social',['except'=>['index','show']]);
    }

    public function index() {
        $params = Input::all();
        $api = new ApiConsume();
        $api->get("contacto",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();
        
        $data = collect($response['data']);

        if(isset($params["id"]))
        {
            return compact("data");
        }
        else{
            return ContactoPublicResource_01::collection($data);
        }
    }

    public function show($id) {
        $params = Input::all();
        $api = new ApiConsume();
        $api->get("contacto/{$id}",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        // El resource requiere que la informacion sea enviada dentro de la variable $data
        $data = $response;
        $data = compact('data');
        $data = collect($data);
        return PersonaPublicResource::collection($data);
    }

    public function store(Request $req)
    {
        $contacto =[];
        $api = new ApiConsume();

        if(isset($req["jwt_user"]["id"])){
            $data = [
                "message" => $req["message"],
                "origin" => $req["origin"],
                "user_social_id" => $req["jwt_user"]["id"],
                "email" => $req["jwt_user"]["email"],
                "username" => $req["jwt_user"]["username"],
                "token" => $req->bearerToken()
            ];

            $contacto = $api->post("contacto",$data);
            if($api->hasError()) { return $api->getError(); }
            $contacto = $api->response();
        }
        
        return compact('contacto');
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
                    $updated = $personas->update($realReq->toArray());

                    return ['updated'=>$updated];
                } else {
                    return ['error'=>'La persona que intenta editar, no corresponde a su perfil'];
                }
            }
        }

        return compact('persona');
    }
}