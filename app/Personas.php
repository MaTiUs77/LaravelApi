<?php

namespace App;

use App\Traits\CustomPaginationScope;
use App\Traits\WithOnDemandTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Personas extends Model
{
    use CustomPaginationScope, WithOnDemandTrait;
    
    protected $table = 'personas';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'apellidos','nombres','sexo','documento_tipo','documento_nro',
        'fecha_nac','email','ciudad_id','telefono_nro','calle_nombre','calle_nro',
        'depto_casa','tira_edificio','observaciones','familiar','alumno',
        'vinculo',
        'pcia_nac','nacionalidad'
    ];

    // Nombre completo dinamico
//    protected $attributes = ['nombre_completo'];
    protected $appends = ['nombre_completo'];

    public function getNombreCompletoAttribute()
    {
        // Actualizar edad (de ser necesario), al solicitar el nombre de la persona
        $realAge = Carbon::parse($this->fecha_nac)->age;
        if($this->edad<$realAge)
        {
            Log::info("Persona: Edad actualizada ({$this->fecha_nac}) {$this->edad} ->$realAge / id:{$this->id}");
            $this->edad = $realAge;
            $this->save();
        }

        return "{$this->apellidos}, {$this->nombres}";
    }

    function Ciudad()
    {
        return $this->hasOne('App\Ciudades', 'id', 'ciudad_id');
    }

    function Alumnos()
    {
        return $this->hasMany('App\Alumnos', 'persona_id', 'id');
    }

    function Barrio()
    {
        return $this->hasOne('App\Barrios', 'id', 'barrio_id');
    }
}
