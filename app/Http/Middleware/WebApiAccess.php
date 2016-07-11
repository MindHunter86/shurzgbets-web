<?php

namespace App\Http\Middleware;

use Closure;

class WebApiAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->get('apikey') != config('webapi.apikey')) return response()->json([
            'success' => false,
            'error' => 'Wrong API Key'
        ]);
        $ip = $request->getClientIp();
        if(!in_array($ip, config('webapi.allowIP'))) return response()->json([
            'success' => false,
            'error' => 'Access restricted'
        ]);
        return $next($request);
    }
}
