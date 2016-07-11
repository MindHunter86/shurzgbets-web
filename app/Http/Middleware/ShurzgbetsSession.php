<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use Auth;

class ShurzgbetsSession
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
        $user = Auth::user();
        if (!$user) {
            Cookie::queue(
                Cookie::forget(config('shurzgbetssession.cookieName'))
            );
        } else {
            if (is_null(Cookie::get(config('shurzgbetssession.cookieName')))) {
                $encode = [
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'steamid64' => $user->steamid64,
                    'trade_link' => $user->trade_link
                ];
                $encodeJSON = json_encode($encode);
                $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
                $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

                $cryptedData = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, config('shurzgbetssession.encryptionKey'),
                    $encodeJSON, MCRYPT_MODE_CBC, $iv);
                $cryptedData = base64_encode($iv . $cryptedData);
                Cookie::queue(
                    Cookie::make(
                        config('shurzgbetssession.cookieName'),
                        $cryptedData,
                        60 * 60 * 24,
                        null,
                        null,
                        config('shurzgbetssession.secure'),
                        true
                    )
                );
            }
        }

        return $next($request);
    }
}
