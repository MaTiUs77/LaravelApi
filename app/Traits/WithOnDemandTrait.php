<?php

namespace App\Traits;


trait WithOnDemandTrait {
    public function scopeWithOnDemand($query,$default=[])
    {
        $with= request('with');
        return $query->with(WithOnDemandTrait::prepare($default,$with));
    }

    public static function bootWithOnDemandTrait()
    {
        /*
         * ATENCION
         *
         * El metodo boot, genera un problema al anidar modelos
         */
        /*
        static::addGlobalScope(function ($query) {
            //$default = $query->getModel()->with;
            $with= request('with');
            $query->with(WithOnDemandTrait::prepare([],$with));
        });
        */
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
