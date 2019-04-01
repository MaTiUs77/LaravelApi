<?php

namespace App\Http\Controllers\Api\Familiares\v1\Request;

use Illuminate\Foundation\Http\FormRequest;

class FamiliarCrudStoreReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'persona_id' => 'required|integer',
            'vinculo' => 'required|string',
            'conviviente' => 'required|boolean',
            'autorizado_retirar' => 'required|boolean',
            'observaciones' => 'string'
        ];
    }

    public function attributes()
    {
        return [
            'persona_id' => 'El campo Persona_ID',
            'vinculo' => 'El campo Vinculo',
            'conviviente' => 'El campo Conviviente',
            'autorizado_retirar' => 'El campo Autorizado a Retirar',
            'observaciones' => 'El campo Observaciones',
        ];
    }

    public function messages()
    {
        return [
            'persona_id.required' => 'Persona_ID es Requerido',
            'vinculo.required' => 'El Vinculo es Requerido',
            'conviviente.required' => 'El campo Conviviente es Requerido',
            'autorizado_retirar.required' => 'Autorizado a Retirar es Requerido',
            'observaciones.required' => 'Observaciones es Requerido',
        ];
    }
}
