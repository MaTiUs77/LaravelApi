<?php

namespace App\Http\Controllers\Api\Contacto\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class ContactoCrudStoreReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'message' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'message' => 'El campo Mensaje',
        ];
    }

    public function messages()
    {
        return [
            'message.required' => 'El Mensaje es Requerido'
        ];
    }
}
