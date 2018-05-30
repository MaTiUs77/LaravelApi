<?php
namespace App\Http\Controllers\Api\Dependencia\RRHH;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Input;

class NominalAlumnosInscriptos extends Controller
{
    public function __construct()
    {
        //$this->middleware('jwt');
    }

    public function start()
    {
        // Consume API lista de inscripciones
        $guzzle = new Client();
        $consumeApi = $guzzle->get(env('API_HOST_REMOTO')."/api/inscripcion/lista",[
            'query' => Input::all()
        ]);

        // Obtiene el contenido de la respuesta, la transforma a json
        $content = $consumeApi->getBody()->getContents();
        $lista = json_decode($content,true);

        // Si no esta definido el error, procedemos a formatear los datos
        if(!isset($lista['error']))
        {
            // Transforma los datos a collection para realizar un mapeo
            $data = collect($lista['data']);

            $formatted = $data->map(function($item){
                $inscripcion = $item['inscripcion'];
                $curso = $item['curso'];

                $ciclo = $inscripcion['ciclo'];
                $centro = $inscripcion['centro'];
                $persona = $inscripcion['alumno']['persona'];

                return [
                    'dni' => $persona['documento_nro'],
                    'nombres' => $persona['nombres'],
                    'apellidos' => $persona['apellidos'],
                    'nombre_completo' => $persona['nombre_completo'],
                    'ciclo' => $ciclo['nombre'],
                    'centro' => $centro['nombre'],
                    'nivel_servicio' => $centro['nivel_servicio'],
                    'año' => $curso['anio'],
                    'division' => $curso['division'],
                    'turno' => $curso['turno'],
                    'fecha_alta' => $inscripcion['fecha_alta'],
                    'fecha_baja' => $inscripcion['fecha_baja'],
                    'fecha_egreso' => $inscripcion['fecha_egreso']
                ];
            });

            $lista['data'] = $formatted;

            return $lista;
        }

        return $lista;
    }
}
