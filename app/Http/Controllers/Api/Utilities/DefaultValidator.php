<?php

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DefaultValidator extends Controller
{
    public static function make($input=array(),$rules=array()) {
        $validator = Validator::make($input,$rules);
        if ($validator->fails()) {
            return response()->json([
                'error_type' => 'ValidationException',
                'error' => $validator->errors()
            ]);
        }
    }
}