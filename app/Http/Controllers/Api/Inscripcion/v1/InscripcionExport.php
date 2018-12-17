<?php
namespace App\Http\Controllers\Api\Inscripcion\v1;

use App\Http\Controllers\Api\Utilities\Export;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class InscripcionExport extends Controller
{
    public function excel()
    {
        $guzzle = new Client();

        $data = $guzzle->get(env('SIEP_LARAVEL_API')."/api/v1/inscripcion/lista",[
            'query' => Input::all()
        ]);

        $json = json_decode($data->getBody());

        if(isset($json->error))
        {
            return response()->json($json);
        } 

        if($json!=null)
        {
            $content = [];
            // Primer fila
            $content[] = ['Ciclo', 'Centro', 'Curso', 'Division', 'Turno', 'DNI', 'Alumno'];

            // Contenido
            foreach($json->data as $index => $item) {
                $content[] = [
                    $item->inscripcion->ciclo->nombre,
                    $item->inscripcion->centro->nombre,
                    $item->curso->anio,
                    $item->curso->division,
                    $item->curso->turno,
                    $item->inscripcion->alumno->persona->documento_nro,

                    $item->inscripcion->alumno->persona->apellidos
                    .",".
                    $item->inscripcion->alumno->persona->nombres
                ];
            }

            Log::debug("Exportar excel Total: $json->total",Input::all());

            Export::toExcel('Inscripciones','Lista',$content);
        } else {

            $error = 'No fue posible generar el archivo excel';
            Log::error("Exportacion a excel",$error);
            return compact('error');
        }
    }
}
