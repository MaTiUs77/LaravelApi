<?php

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class Export extends Controller
{
    public static function resourceToExcel($archivo,$sheet,$resource,$customHeader=[])
    {
        // Obtiene el nombre de las variables para la cabecera
        if(count($customHeader)>0) {
            $header = $customHeader;
        } else {
            $header = collect($resource->first())->keys()->toArray();
        }

        // Quita las variables y deja solo los valores
        $contenido = collect($resource)->map(function($i){
            return collect($i)->flatten();
        })->toArray();

        // Agrega la cabecera al principio del array
        array_unshift($contenido,$header);
        Export::toExcel($archivo,$sheet,$contenido);
    }

    public static function toExcel($archivo,$sheet,$content) {
        Excel::create($archivo, function($excel) use($content,$sheet) {
            $excel->sheet($sheet, function($sheet) use($content) {
                $sheet->fromArray($content, null, 'A1', false, false);
            });
        })->export('xls');
    }
}