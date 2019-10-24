<?php

namespace App\Http\Controllers\Api\Pases\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
use App\Pases;
use App\PasesTrazabilidad;
use Illuminate\Http\Request;

class PasesCrud extends Controller
{
    // Index
    public function index(Request $req)
    {
        $params = request()->all();
        $default['pase'] = 'con';
        $default['with'] = 'inscripcion.origen';
        /*

            $default['estado_inscripcion'] = 'CONFIRMADA';
            $default['nivel_servicio'] = [
                    'Comun - Primario',
                    'Comun - Secundario',
                ];
        */
        $params = array_merge($params,$default);

        // Consumo API Personas
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);

        if($api->hasError()) { return $api->getError(); }

        return $api->response();
    }

    // View
    public function show($id)
    {
        $pase = Pases::find($id);
        $pasetraza = PasesTrazabilidad::where('id',$id)->get();
        return compact('pase','pasetraza');
    }

    // Create
    public function store()
    {
        $pase = new Pases();
        $pase->inscripcion_id = request('inscripcion_id');
        $pase->centro_id= request('centro_id');

        $pase->centro_id_destino_a = request('centro_id_destino_a');
        $pase->centro_id_destino_b = request('centro_id_destino_b');
        $pase->anio= request('anio');

        $pase->nota_pase_tutor = request('nota_pase_tutor');

        $pase->tipo= request('tipo');
        $pase->motivo= request('motivo');

        $pase->observaciones= request('observaciones');

        $pase->user_id= request('user_id');
        $pase->familiar_id= request('familiar_id');

        $pase->fecha_vencimiento= request('fecha_vencimiento');
        $pase->save();

        $pasetraza = PasesTrazabilidad::create($pase->toArray());

        return compact('pase','pasetraza');
    }

    // Edit
    public function update($id)
    {
        $pase = Pases::findOrFail($id);
        $pase->anio= 3;
        $pase->user_id= 12;
        $pase->save();

        $pasetraza = PasesTrazabilidad::create($pase->toArray());

        return compact('pase','pasetraza');
    }

    // Delete
    public function destroy($id)
    {
        $pase = Pases::findOrFail($id)->delete();
        $pasetraza = PasesTrazabilidad::where('id',$id)->delete();

        return compact('pase','pasetraza');
    }
}
