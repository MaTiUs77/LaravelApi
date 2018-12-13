<?php

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Controller;

class WithOnDemand extends Controller
{
    public static function set($withDefault=array(),$withAppend=null) {
        // Adjunta "with" al modelo
        if($withAppend) {
            $appendWith = explode(',',$withAppend);
            $withDefault = collect($withDefault)->merge($appendWith)->unique()->toArray();
        }
        return $withDefault;
    }
}