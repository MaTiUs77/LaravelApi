<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Input;

class authJWTSocial
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

        try {
            $basicauth = new Client(['base_uri' => env('SIEP_AUTH_API')]);
            $authResponse = $basicauth->request('GET','/social/me', [
                'headers' => [
                    'Authorization' => "Bearer {$token}"
                ]
            ]
            )->getBody()->getContents();

            $jwt_user = json_decode($authResponse, true);
            $jwt_user['auth'] = 'jwt.social';
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
