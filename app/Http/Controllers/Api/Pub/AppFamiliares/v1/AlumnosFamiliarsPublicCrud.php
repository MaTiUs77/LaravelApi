<?php

namespace App\Http\Controllers\Api\Pub\AppFamiliares\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\AlumnosFamiliar;
use App\Http\Controllers\Api\AlumnosFamiliars\v1\Request\AlumnosFamiliarsCrudStoreReq;
use App\Resources\AlumnoFamiliarPublicResource_02;
use App\Resources\AlumnoFamiliarPublicResource_01;
use App\Resources\AlumnoFamiliarPublicResource_03;
use App\Http\Controllers\Api\AlumnosFamiliars\v1\Request\AlumnosFamiliarsCrudUpdateReq;

class AlumnosFamiliarsPublicCrud extends Controller
{
    public function index() {
        $params = Input::all();
        $api = new ApiConsume();
        $api->get("alumnos_familiars",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();
        
        $data = collect($response['data']);

        if(isset($params["persona_id"]))
        { 
            return compact(["alumnos_familiars"=>$data]);
        }
        else{
            return AlumnoFamiliarPublicResource_01::collection($data);
        }
    }

    public function show($id) {
        $params = Input::all();
        $api = new ApiConsume();
        $api->get("alumnos_familiars/{$id}",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        // El resource requiere que la informacion sea enviada dentro de la variable $data
        $data = $response;
        $data = compact('data');
        $data = collect($data);
        return AlumnoFamiliarPublicResource_02::collection($data);
    }

    public function store(AlumnosFamiliarsCrudStoreReq $req)
    {
        $api = new ApiConsume();

        // Verificar existencia del alumno, segun pesona_id
        $api->get("alumnos_familiars",["alumno_id"=>$req["alumno_id"],"familiar_id"=>$req["familiar_id"]]);
        if($api->hasError()) { return $api->getError(); }
        $alumnos_familiars = $api->response();
        $alumnos_familiars = collect($alumnos_familiars["data"]);
        // Si no existe el alumnos_familiars... se crea!
        if($alumnos_familiars->isEmpty()) {
            // Se crea la alumnos_familiar
            $alumnos_familiars = AlumnosFamiliar::create($req->all());
        }
        else {
            $alumnos_familiars = (object) $alumnos_familiars[0];
        }

        return compact('alumnos_familiars');
    }

    public function update($id,AlumnosFamiliarsCrudUpdateReq $req)
    {
        $relacion = AlumnosFamiliar::findOrFail($id);
        switch(request('mode')) {
            case 'confirmar':
                $relacion->status = 'confirmada';
                break;
            case 'cancelar':
                $relacion->status = 'revisar';
                break;
            case 'pendiente':
                $relacion->status = 'pendiente';
                break;
        };

        $relacion->save();

        return $relacion;
    }

    // Busca los Alumnos para un familiar
    public function getByFamiliar($familiar_id)
    {
        $alumnos = AlumnosFamiliar::withOnDemand(['alumno.persona'])->where('familiar_id',$familiar_id)->get();
        return AlumnoFamiliarPublicResource_03::collection($alumnos);
    }
}