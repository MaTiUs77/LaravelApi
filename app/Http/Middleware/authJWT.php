<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class authJWT
{
    public function handle($request, Closure $next)
    {
        // Verifica token en los parametros
        $token = $request->get('token');
        if (!$token) {
            // Si no esta definido, busca token en Bearer de Authentication
            $token = $request->bearerToken();
        }

        // Si el token sigue indefinido.. se encuentra missing
        if (!$token) {
            $code = 401;
            return response([
                'code' => $code,
                'error' => "token_missing"
            ], $code);
        }

        // Verifica datos de JWT contra User
        try {
            $basicauth = new Client(['base_uri' => env('SIEP_AUTH_API')]);
            $authResponse = $basicauth->request('GET','/me', [
                    'headers' => [
                        'Authorization' => "Bearer {$token}"
                    ]
                ]
            )->getBody()->getContents();

            $jwt_user = json_decode($authResponse, true);
            $jwt_user['auth'] = 'jwt';
            // Enviar userModel al controlador
            $request->merge(compact('jwt_user'));
        } catch (BadResponseException $ex) {
            $resp = $ex->getResponse();
            $jsonBody = json_decode($resp->getBody(), true);
            return response()->json($jsonBody);
        } catch (\Exception $ex) {
            $resp = $ex->getMessage();
            return response()->json([
                'error'=>$resp
            ]);
        }

        return $next($request);
    }
}