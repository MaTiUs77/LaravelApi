<?php
namespace App\Http\Controllers\Api\Inscripcion;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class InscripcionExport extends Controller
{
    public function excel()
    {
        $guzzle = new Client();
        $data = $guzzle->get("http://localhost/api/inscripcion/lista",[
            'query' => Input::all()
        ]);

        $json = json_decode($data->getBody());

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

        Excel::create('Inscripciones', function($excel) use($content) {
            $excel->sheet('Lista', function($sheet) use($content) {
                $sheet->fromArray($content, null, 'A1', false, false);
            });
        })->export('xls');
    }
}
