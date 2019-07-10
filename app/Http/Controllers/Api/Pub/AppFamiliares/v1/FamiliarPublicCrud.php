<?php

namespace App\Http\Controllers\Api\Pub\AppFamiliares\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Resources\FamiliarPublicResource_01;
use App\Resources\FamiliarPublicResource_02;
use App\Http\Controllers\Api\Familiares\v1\Request\FamiliarCrudIndexReq;
use App\Http\Controllers\Api\Familiares\v1\Request\FamiliarCrudStoreReq;
use App\Http\Controllers\Api\Familiares\v1\Request\FamiliarCrudUpdateReq;
use App\Familiar;

class FamiliarPublicCrud extends Controller
{
    public function index() {
        $params = Input::all(); 
        $api = new ApiConsume();
        
        $api->get("familiar",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();
        
        $data = collect($response['data']);

        if(isset($params["id"]))
        { 
            return FamiliarPublicResource_02::collection($data);
        }
        else{
            return FamiliarPublicResource_01::collection($data);
        }
    }

    public function show($id) {
        $api = new ApiConsume();
        $api->get("familiar/{$id}");
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        // El resource requiere que la informacion sea enviada dentro de la variable $data
        $data = $response;
        $data = compact('data');
        $data = collect($data);
        return FamiliarPublicResource_02::collection($data);
    }

    public function store(FamiliarCrudStoreReq $req)
    {
        $api = new ApiConsume(null,'api/public/app_familiares/v1');
        // Verificar existencia de la persona, segun persona_id
        $api->get("familiar",["persona_id"=>$req["persona_id"]]);
        if($api->hasError()) { return $api->getError(); }
        $familiar = $api->response();
        $familiar = collect($familiar["data"]);
        // Si no existe la familiar... se crea!
        if($familiar->isEmpty()) {
            // Se crea la persona
            $familiar = Familiar::create($req->all());
        }

        $familiar = $familiar[0];
        
        return compact('familiar');
    }

     // Busca un familiar por persona_id
     public function getByPersonaId($persona_id)
     {
        $api = new ApiConsume();
        $api->get("familiar",["persona_id"=>$persona_id]);
        if($api->hasError()) { return $api->getError(); }
        $familiar = $api->response();
        $familiar = collect($familiar["data"]);
        return $familiar;
        if($familiar->isNotEmpty())
        {
            $familiar = (object) $familiar[0];
        }
         return $familiar;
     }
}