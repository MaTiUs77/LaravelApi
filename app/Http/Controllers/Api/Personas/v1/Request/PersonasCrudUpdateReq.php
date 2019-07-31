<?php

namespace App\Http\Controllers\Api\Personas\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class PersonasCrudUpdateReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'apellidos' => 'required|string',
            'nombres' => 'required|string',
            'sexo' => 'required|string',
            'documento_tipo' => 'required|string',
            'documento_nro' => 'required|numeric',
            'fecha_nac' => 'required|date',
            'email' => 'email',
            'telefono_nro' => 'required|string',
            'telefono_nro_alt' => 'string',
            'calle_nombre' => 'string',
            'calle_nro' => 'numeric',

            'ciudad' => 'string|exists:ciudads,nombre',

            'depto_casa' => 'string',
            'tira_edificio' => 'string',
            'observaciones' => 'string',
            'pcia_nac' => 'string',
            'nacionalidad' => 'string',

            'familiar' => 'boolean',
            'alumno' => 'boolean',
        ];
    }

    public function attributes()
    {
        return [
            'familiar' => 'El campo familiar',
            'alumno' => 'El campo alumno',
        ];
    }

    public function messages()
    {
        return [
            'apellidos.required' => 'El apellido es requerido',
            'fecha_nac.required' => 'La fecha de nacimiento es requerida',

            'ciudad.exists' => 'La ciudad solicitada no existe',

            'familiar.boolean' => 'El campo familiar debe ser 0 o 1',
            'alumno.boolean' => 'El campo alumno debe ser 0 o 1',

        ];
    }
}
