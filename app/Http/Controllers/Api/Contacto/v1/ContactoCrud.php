<?php

namespace App\Http\Controllers\Api\Contacto\v1;

use App\Http\Controllers\Api\Contacto\v1\Request\ContactoCrudIndexReq;
use App\Http\Controllers\Api\Contacto\v1\Request\ContactoCrudStoreReq;
use App\Http\Controllers\Api\Contacto\v1\Request\ContactoCrudUpdateReq;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;
use App\Contacto;
use App\UserSocial;
use Illuminate\Http\Request;
use App\Mail\Email;
use Illuminate\Support\Facades\Mail;

class ContactoCrud extends Controller
{
    public function __construct(Request $req)
    {
        $this->middleware('jwt.social',['except'=>['index','show']]);
    }

    // List
    public function index(ContactoCrudIndexReq $req)
    {
        
        $contacto = new Contacto();

        $contacto->when(request('id'), function ($q, $v) {
            return $q->findOrFail($v);
        });

        $contacto->when(request('user_social_id'), function ($q, $v) {
            return $q->where('user_social_id',$v);
        });

        return $contacto->customPagination();
    }

    // View
    public function show($id)
    {
        // Se validan los parametros
        $input = ['id'=>$id];
        $rules = ['id'=>'numeric'];

        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        // Continua si las validaciones son efectuadas
        $contacto = new Contacto();
        return $contacto->findOrFail($id);
    }

    // Create
    public function store(ContactoCrudStoreReq $req)
    {
        // Se crea el mensaje de contacto
        if(isset($req["jwt_user"]["id"])){
            $data = [
                "message" => $req["message"],
                "origin" => $req["origin"],
                "user_social_id" => $req["jwt_user"]["id"],
                "email" => $req["jwt_user"]["email"],
                "username" => $req["jwt_user"]["username"]
            ];
            $contacto = Contacto::create($data);
            if($contacto){
                Mail::to('sinide.tdf@gmail.com','Receptor de Prueba')
                    ->send(new Email($data));
            }
        }
        return compact('contacto');
    }

    // Update
    public function update($id,ContactoCrudUpdateReq $req)
    {
        // Verificar existencia de la persona, segun DNI
        $contacto = Contacto::findOrFail($id);

        // Si existe la contacto... se actualiza!
        if($contacto) {
            // Se actualiza el mensaje
            $updated = $contacto->update($req->toArray());

            return ['updated'=>$updated];
        }

        return compact('contacto');
    }
}