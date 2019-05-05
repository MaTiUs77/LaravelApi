<?php

namespace App\Http\Controllers\Api\Contacto\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class ContactoCrudIndexReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'numeric',
            'user_social_id' => 'numeric',
            'message' => 'string',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'ID del Mensaje',
            'user_social_id' => 'ID del Usuario Social',
            'familiar' => 'El Mensaje',
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
