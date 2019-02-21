<?php

namespace App\Http\Controllers\Api\Personas\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class PersonasCrudIndexReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'numeric',
            'nombres' => 'string',
            'documento_nro' => 'numeric',
            'familiar' => 'boolean',
            'alumno' => 'boolean',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'ID de la Persona',
            'familiar' => 'El campo familiar',
            'alumno' => 'El campo alumno',
        ];
    }

    public function messages()
    {
        return [
            'familiar.boolean' => 'El campo familiar debe ser verdadero o falso',
            'alumno.boolean' => 'El campo alumno debe ser verdadero o falso',

        ];
    }
}
