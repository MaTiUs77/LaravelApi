<?php

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class Export extends Controller
{
    public static function toExcel($archivo,$sheet,$content) {
        Excel::create($archivo, function($excel) use($content,$sheet) {
            $excel->sheet($sheet, function($sheet) use($content) {
                $sheet->fromArray($content, null, 'A1', false, false);
            });
        })->export('xls');
    }
}