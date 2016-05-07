<?php

namespace App\Http\Middleware;

use Closure;

class SecretKey
{
    const SECRET_KEY = '1K2wmLstWcMTirHs2rEeOKvyxDTkZVCclceg41Qqb2f6QJ2FaDEWtoUTpMjgBtjY';
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->get('secretKey') != self::SECRET_KEY) return response()->json('Invalid Secret Key');
        return $next($request);
    }
}
