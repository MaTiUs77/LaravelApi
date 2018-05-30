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

        $guzzle = new Client();
        try {

            $resp = $guzzle->get(env('API_AUTH_HOST')."/me",[
                'query' => Input::all()
            ]);

            $userModel = json_decode($resp->getBody(), true);
            // Enviar userModel al controlador

        } catch (BadResponseException $ex) {
            $resp = $ex->getResponse();
            $jsonBody = json_decode($resp->getBody(), true);
            return response()->json($jsonBody);
        }

        return $next($request);
    }
}
