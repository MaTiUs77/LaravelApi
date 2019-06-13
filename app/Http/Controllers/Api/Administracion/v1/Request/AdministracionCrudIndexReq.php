<?php

namespace App\Http\Controllers\Api\Administracion\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class AdministracionCrudIndexReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'numeric',
            'request_from' => 'string',
            'en_mantenimiento' => 'boolean',
            'stage' => 'string'
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'ID del key',
            'request_from' => 'La Procedencia'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
