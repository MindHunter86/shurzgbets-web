<?php

namespace App\Http\Middleware;

use Closure;

class SecretAccess
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
//        if(!$request->user()->is_admin){
//           abort(404);
//        }
        if (!$request->user()->secretAllow()) {
            abort(404);
        }

        return $next($request);
    }
}
