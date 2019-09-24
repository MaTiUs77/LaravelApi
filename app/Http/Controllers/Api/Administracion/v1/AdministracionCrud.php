<?php

namespace App\Http\Controllers\Api\Administracion\v1;

use App\Http\Controllers\Api\Administracion\v1\Request\AdministracionCrudIndexReq;
use App\Http\Controllers\Api\Administracion\v1\Request\AdministracionCrudStoreReq;
use App\Http\Controllers\Api\Administracion\v1\Request\AdministracionCrudUpdateReq;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;
use App\Administracion;
use App\UserSocial;
use Illuminate\Http\Request;
use App\Mail\Email;
use Illuminate\Support\Facades\Mail;

class AdministracionCrud extends Controller
{
    public function __construct(Request $req)
    {
        $this->middleware('jwt.social',['except'=>['index','show']]);
    }

    // List
    public function index(AdministracionCrudIndexReq $req)
    {
        
        $administracion = Administracion::withOnDemand();

        if(request('request_from') != null){
            $administracion->where('request_from',request('request_from'));
        }

        if(request('stage') != null){
            $administracion->where('stage',request('stage'));
        }

        // $administracion->when(request('user_social_id'), function ($q, $v) {
        //     return $q->where('user_social_id',$v);
        // });

        return $administracion->first();
    }

    // View
    public function show($id)
    {
        // Se validan los parametros
        $input = ['id'=>$id];
        $rules = ['id'=>'numeric'];

        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        // Continua si las validaciones son efectuadas
        $administracion = new Administracion();
        return $administracion->findOrFail($id);
    }

    // Create
    public function store(AdministracionCrudStoreReq $req)
    {
        // Se crea el mensaje de contacto
        if(isset($req["jwt_user"]["id"])){
            $data = [
                "request_from" => $req["request_from"],
                "en_mantenimiento" => $req["en_mantenimiento"],
                "stage" => $req["stage"]
            ];
            $administracion = Administracion::create($data);
        }
        return compact('administracion');
    }

    // Update
    public function update($id,AdministracionCrudUpdateReq $req)
    {
        // Verificar existencia de la persona, segun DNI
        $administracion = Administracion::findOrFail($id);

        // Si existe la administracion... se actualiza!
        if($administracion) {
            // Se actualiza el mensaje
            $updated = $administracion->update($req->toArray());

            return ['updated'=>$updated];
        }

        return compact('administracion');
    }
}