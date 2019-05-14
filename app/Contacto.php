<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\WithOnDemandTrait;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use WithOnDemandTrait, CustomPaginationScope;

    protected $table = 'contacto';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'user_social_id','message','username','email','origin'
    ];
}
