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
            // Por defecto la lista se ordena por APELLIDOS y NOMBRES
            $collection = collect($json->data);
            $sorted = $collection->sortBy(function ($item, $key) {
                return trim($item->inscripcion->alumno->persona->apellidos).",".$item->inscripcion->alumno->persona->nombres;
            })->values();

            $content = [];
            // Primer fila
            $content[] = ['Ciclo', 'Centro', 'Curso', 'Division', 'Turno', 'DNI', 'Alumno','Estado'];

            // Contenido
            foreach($sorted as $index => $item) {
                $content[] = [
                    $item->inscripcion->ciclo->nombre,
                    $item->inscripcion->centro->nombre,
                    $item->curso->anio,
                    $item->curso->division,
                    $item->curso->turno,
                    $item->inscripcion->alumno->persona->documento_nro,
                    trim($item->inscripcion->alumno->persona->apellidos).",".title_case($item->inscripcion->alumno->persona->nombres),
                    $item->inscripcion->estado_inscripcion
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
