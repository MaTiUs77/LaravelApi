<?php

namespace App\Http\Controllers\Api\Pases\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Controller;
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
    }

    // Create
    public function store()
    {
    }

    // Edit
    public function update($id)
    {
    }

    // Delete
    public function destroy($id)
    {
    }
}
