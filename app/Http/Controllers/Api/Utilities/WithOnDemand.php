<?php

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Controller;

class WithOnDemand extends Controller
{
    public static function set($withDefault=array(),$withAppend=null) {
        // Adjunta "with" al modelo
        if($withAppend) {
            $appendWith = explode(',',$withAppend);
            $uniques= collect($withDefault)->merge($appendWith)->unique();

            return $uniques->transform(function ($item) {
                return strtolower($item);
            })->toArray();
        }

        return $withDefault;
    }
}