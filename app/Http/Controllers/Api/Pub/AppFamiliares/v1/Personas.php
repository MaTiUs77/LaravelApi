<?php

namespace App\Http\Controllers\Api\Pub\AppFamiliares\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
use App\Resources\PersonaPublicResource;
use Illuminate\Support\Facades\Input;

class Personas extends Controller
{
    public function index() {
        $params = Input::all();
        $api = new ApiConsume();
        $api->get("personas",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        $data = collect($response['data']);
        return PersonaPublicResource::collection($data);
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
}