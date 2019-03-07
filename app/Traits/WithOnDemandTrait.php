<?php

namespace App\Traits;

trait WithOnDemandTrait {
    public static function bootWithOnDemandTrait()
    {
        static::addGlobalScope(function ($query) {
            //$default = $query->getModel()->with;
            $with= request('with');
            $query->with(WithOnDemandTrait::prepare([],$with));
        });
    }

    public static function prepare($withDefault=array(),$withAppend=null) {
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
