<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomPaginationScope;
use App\Traits\WithOnDemandTrait;

class Administracion extends Model
{
    use CustomPaginationScope, WithOnDemandTrait;

    protected $table = 'administracion';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    protected $fillable = [
        'request_from','en_mantenimiento','stage','created_at','updated_at'
    ];
}
