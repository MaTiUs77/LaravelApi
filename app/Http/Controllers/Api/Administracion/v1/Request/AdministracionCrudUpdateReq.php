<?php

namespace App\Http\Controllers\Api\Administracion\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class AdministracionCrudUpdateReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_social_id' => 'required|integer',
            'message' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'user_social_id' => 'El ID de UserSocial',
            'message' => 'El campo Mensaje',
        ];
    }

    public function messages()
    {
        return [
            'user_social_id.required' => 'El ID de UserSocial es Requerido',            
            'message.required' => 'El Mensaje es Requerido'
        ];
    }
}
