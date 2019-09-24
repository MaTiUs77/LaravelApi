<?php

namespace App\Http\Controllers\Api\Familiares\v1;

use App\Http\Controllers\Api\Familiares\v1\Request\FamiliarCrudIndexReq;
use App\Http\Controllers\Api\Familiares\v1\Request\FamiliarCrudStoreReq;
use App\Http\Controllers\Api\Familiares\v1\Request\FamiliarCrudUpdateReq;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;

use App\UserSocial;
use App\Familiar;
use Illuminate\Http\Request;

class FamiliarCrud extends Controller
{
    public function __construct(Request $req)
    {
        $this->middleware('jwt.social',['except'=>['index','show','getByPersonaId']]);
    }

    // List
    public function index(FamiliarCrudIndexReq $req)
    {
        $familiar = Familiar::withOnDemand();
        $familiar->when(request('id'), function ($q, $v) {
            return $q->findOrFail($v);
        });

        $familiar->when(request('persona_id'), function ($q, $v) {
            return $q->where('persona_id',$v);
        });

        // $familiar->when(request('user_social_id'), function ($q, $v) {
        //     return $q->where('user_social_id',$v);
        // });

        return $familiar->customPagination();
    }

    // View
    public function show($id)
    {
        // Se validan los parametros
        $input = ['id'=>$id];
        $rules = ['id'=>'numeric'];

        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        // Continua si las validaciones son efectuadas
        $familiar = Familiar::withOnDemand();
        return $familiar->findOrFail($id);
    }

    // Create
    public function store(FamiliarCrudStoreReq $req)
    {
        // Verificar existencia del familiar, segun persona_id
        $familiar = Familiar::where('persona_id',request('persona_id'))->first();

        // Si no existe el familiar... crea el familiar
        if(!$familiar) {
            // Se crea el familiar
            $familiar = Familiar::create($req->all());
        }

        return compact('familiar');
    }

    // Update
    public function update($id,FamiliarCrudUpdateReq $req)
    {
        // Verificar existencia de la persona, segun DNI
        $familiar = Familiar::findOrFail($id);

        // Si existe la familiar... se actualiza!
        if($familiar) {
            // Se actualiza el mensaje
            $updated = $familiar->update($req->toArray());

            return ['updated'=>$updated];
        }

        return compact('familiar');
    }

    // Busca un familiar por persona_id
    public function getByPersonaId($persona_id)
    {
        $familiar = Familiar::where('persona_id',$persona_id)->first();
        return $familiar;
    }
}