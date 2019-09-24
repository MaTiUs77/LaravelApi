<?php

namespace App\Http\Middleware;

use Closure;

class Cake
{
    public function handle($request, Closure $next)
    {
        if(!$request->headers->has(env('XHOSTCAKE')))
        {
            return response([
                'code' => 401,
                'error' => "invalid_host"
            ], 401);
        }

        return $next($request);
    }
}