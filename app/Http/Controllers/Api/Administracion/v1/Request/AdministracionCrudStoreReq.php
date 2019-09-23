<?php

namespace App\Http\Controllers\Api\Administracion\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class AdministracionCrudStoreReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'message' => 'required|string',
            'origin' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'message' => 'El campo Mensaje',
            'origin' => 'El campo Origen',
        ];
    }

    public function messages()
    {
        return [
            'message.required' => 'El Mensaje es Requerido',
            'origin.required' => 'El Origen es Requerido'
        ];
    }
}
