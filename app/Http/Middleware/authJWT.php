<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Input;

class authJWT
{
    public function handle($request, Closure $next)
    {
        if (!$request->has('token')) {
            $code = 401;
            return response([
                'code' => $code,
                'error' => "token_missing"
            ], $code);
        }


        try {
            $basicauth = new Client(['base_uri' => env('SIEP_AUTH_API')]);
            $token = Input::get('token');
            $authResponse = $basicauth->request('GET','/me', [
                'headers' => [
                    'Authorization' => "Bearer {$token}"
                ]
            ]
            )->getBody()->getContents();

            $jwt_user = json_decode($authResponse, true);
            // Enviar userModel al controlador
            $request->merge(compact('jwt_user'));

        } catch (BadResponseException $ex) {
            $resp = $ex->getResponse();
            $jsonBody = json_decode($resp->getBody(), true);
            return response()->json($jsonBody);
        }

        return $next($request);
    }
}
