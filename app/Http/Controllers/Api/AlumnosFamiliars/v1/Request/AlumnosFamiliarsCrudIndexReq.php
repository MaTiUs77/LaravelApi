<?php

namespace App\Http\Controllers\Api\AlumnosFamiliars\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class AlumnosFamiliarsCrudIndexReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
        ];
    }

    public function attributes()
    {
        return [
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
