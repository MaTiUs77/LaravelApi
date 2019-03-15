<?php

namespace App\Http\Controllers\Api\Exportar\v1;

use App\Http\Controllers\Api\Exportar\v1\Resources\ListaAlumnosResource;
use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Api\Utilities\Export;
use App\Http\Controllers\Controller;
use Prophecy\Exception\Doubler\MethodNotExtendableException;

class Exportar extends Controller
{
    /*public function excel($render) {
        $params = request()->all();

        // Este metodo magico, permite acceder via URL al render solicitado
        try {
            return $this->$render($params);
        } catch (\Exception $ex) {
            if($ex instanceof MethodNotExtendableException)
            {
                return ['error' => 'Render no disponible'];
            } else {
                return ['error' => $ex->getMessage()];
            }
        }
    }*/

    public function ListaAlumnos() {
        $params = request()->all();
        
        // Consumo API Inscripciones
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        // Prepara un collection y ordena por apellido
        $data = collect($response['data']);
        $sorted = $data->sortBy('inscripcion.alumno.persona.nombre_completo')->values();

        // Transforma el resultado, con un formato pre-establecido
        $alumnos = ListaAlumnosResource::collection(collect($sorted));

        // Obtiene el nombre de las variables
        $header = collect($alumnos->first())->keys()->toArray();

        // Quita las variables y deja solo los valores
        $excel = collect($alumnos)->map(function($i){
           return collect($i)->flatten();
        })->toArray();

        // Se prepara para renderizar el excel
        if(count($excel)>0)
        {
            array_unshift($excel,$header);
            Export::toExcel('ListaAlumnos','Lista de Alumnos',$excel);
        } else {
            return ['error' => 'No se obtuvieron resultados con el filtro aplicado'];
        }
    }
}