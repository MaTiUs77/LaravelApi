<?php

namespace App\Http\Controllers\Api\Familiares\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class FamiliarCrudIndexReq extends FormRequest
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
