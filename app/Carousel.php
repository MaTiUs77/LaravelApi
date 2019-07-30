<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomPaginationScope;
use App\Traits\WithOnDemandTrait;

class Carousel extends Model
{
    use CustomPaginationScope, WithOnDemandTrait;

    protected $table = 'carousel';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    protected $fillable = [
        'url','mobile','desktop','enabled','created_at','updated_at'
    ];
}
