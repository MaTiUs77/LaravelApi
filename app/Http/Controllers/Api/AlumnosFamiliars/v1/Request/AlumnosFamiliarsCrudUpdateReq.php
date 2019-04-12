<?php

namespace App\Http\Controllers\Api\AlumnosFamiliars\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class AlumnosFamiliarsCrudUpdateReq extends FormRequest
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
