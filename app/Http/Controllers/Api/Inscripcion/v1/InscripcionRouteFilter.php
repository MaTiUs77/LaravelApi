<?php
namespace App\Http\Controllers\Api\Inscripcion\v1;

use App\CursosInscripcions;
use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class InscripcionRouteFilter extends Controller
{
    public function index($ciclo, $centro_id=null, $curso_id=null)
    {
        $params = request()->all();
        $default['ciclo'] = $ciclo;
        $default['centro_id'] = $centro_id;
        $default['curso_id'] = $curso_id;

        $params = array_merge($params,$default);

        // Consumo API Inscripciones
        $apiPersona= new ApiConsume();
        $apiPersona->get("inscripcion/lista",$params);
        if($apiPersona->hasError()) { return $apiPersona->getError(); }

        return $apiPersona->response();
    }
}
