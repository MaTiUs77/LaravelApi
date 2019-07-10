<?php

namespace App\Http\Controllers\Api\Pub\AppFamiliares\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Alumnos;
use App\Http\Controllers\Api\Alumnos\v1\Request\AlumnosCrudStoreReq;
use App\Resources\AlumnoPublicResource_02;
use App\Resources\AlumnoPublicResource_01;

class AlumnoPublicCrud extends Controller
{
    public function index() {
        $params = Input::all();
        $api = new ApiConsume();
        $api->get("alumnos",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();
        $data = collect($response['data']);

        if(isset($params["persona_id"]))
        { 
            return compact(["alumnos_familiars"=>$data]);
        }
        else{
            return AlumnoPublicResource_01::collection($data);
        }
    }

    public function show($id) {
        $params = Input::all();
        $api = new ApiConsume();
        $api->get("alumnos/{$id}",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        // El resource requiere que la informacion sea enviada dentro de la variable $data
        $data = $response;
        $data = compact('data');
        $data = collect($data);
        return AlumnoPublicResource_02::collection($data);
    }

    public function store(AlumnosCrudStoreReq $req)
    {
        $api = new ApiConsume();

        // Verificar existencia del alumno, segun pesona_id
        $api->get("alumnos",["persona_id"=>$req["persona_id"]]);
        if($api->hasError()) { return $api->getError(); }
        $alumno = $api->response();
        $alumno = collect($alumno["data"]);
        // Si no existe el alumno... se crea!
        if($alumno->isEmpty()) {
            // Se crea la alumno
            $alumno = Alumnos::create($req->all());
        }
        else {
            $alumno = (object) $alumno[0];
        }

        return compact('alumno');
    }

    public function getByPersonaId($persona_id)
    {
        $familiar = Alumnos::where('persona_id',$persona_id)->orderBy('id','desc')->first();
        return $familiar;
    }
}