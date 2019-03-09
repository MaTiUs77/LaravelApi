<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;

trait CustomPaginationScope {

    function scopeCustomPagination($query,$per_page=10) {
        if($per_page=='all') {
            $countQuery= $query->count();
            $result = $query->paginate($countQuery);
        } else {
            if(!is_numeric($per_page)) {
                $per_page = 10;
            }
            $result = $query->paginate($per_page);
        }

        if(!$result)
        {
            return ['error'=>'Error al paginar'];
        } else {
            if($result instanceof LengthAwarePaginator) {
                return $result->appends(Input::all());
            } else {
                $data = $result;
                return compact('data');
            }
        }
    }
}
