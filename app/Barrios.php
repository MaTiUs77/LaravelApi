<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\WithOnDemandTrait;
use Illuminate\Database\Eloquent\Model;

class Barrios extends Model
{
    use WithOnDemandTrait, CustomPaginationScope;

    protected $table = 'barrios';
}
