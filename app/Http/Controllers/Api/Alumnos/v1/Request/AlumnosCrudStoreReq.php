<?php

namespace App\Http\Controllers\Api\Alumnos\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class AlumnosCrudStoreReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'persona_id' => 'required|integer',
            'centro_id' => 'required|integer',
            'legajo_fisico_nro' => 'integer',
            'pendiente' => 'integer'
        ];
    }

    public function attributes()
    {
        return [
            'persona_id' => 'El campo Persona_ID',
            'centro_id' => 'El campo Centro_ID',
            'legajo_fisico_nro' => 'El campo Legajo',
            'pendiente' => 'El campo Pendiente',
        ];
    }

    public function messages()
    {
        return [
            'persona_id.required' => 'Persona_ID es Requerido',
            'centro_id.required' => 'El Centro es Requerido',
            'pendiente.required' => 'Pendiente es Requerido'
        ];
    }
}
