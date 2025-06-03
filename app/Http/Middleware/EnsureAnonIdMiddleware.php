<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class oı
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->user() && !$request->cookie('anon_id')) {
            $anonId = (string) Str::uuid();
            Cookie::queue('anon_id', $anonId, 60 * 24 * 365);
        }


        return $response;
    }
}
