<?php

namespace App\Http\Controllers\Api\AlumnosFamiliars\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class AlumnosFamiliarsCrudStoreReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'familiar_id' => 'required|integer',
            'alumno_id' => 'required|integer',
            'pendent' => 'required|integer'
        ];
    }

    public function attributes()
    {
        return [
            'familiar_id' => 'El campo Familiar_ID',
            'alumno_id' => 'El campo Alumno_ID',
            'pendent' => 'El Campo Pendiente'
        ];
    }

    public function messages()
    {
        return [
            'familiar_id.required' => 'Familiar_ID es Requerido',
            'alumno_id.required' => 'El Alumno es Requerido',
            'pendent.required' => 'El estado Pendiente es Requerido'
        ];
    }
}
