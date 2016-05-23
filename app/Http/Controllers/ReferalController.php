<?php

namespace App\Http\Controllers;

use App\User;
use App\Promo;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


class ReferalController extends Controller {
    private function checkGame($steamid,$game=false) {
        $res = file_get_contents(sprintf('http://steamcommunity.com/profiles/%s/games/?tab=all&xml=1',$steamid));
        if ($game) {
            return (strpos($res,'<appID>'.$game.'</appID>') !== false);
        } else {
            return $res;
        }
    }
    public function accept(Request $request) {
        $code = $request->get('code');
        if(!$request->has('code')) {
            return response()->json(['success' => false, 'text' => 'Невозможно активировать данный промо код!']);
        }
        if(!empty(strlen(Auth::user()->promo))) {
            return response()->json(['success' => false, 'text' => 'Вы уже активировали промо код']);
        }
        if(empty(Auth::user()->accessToken)) {
            return response()->json(['success' => false, 'text' => 'Вы не ввели ссыклу на обмен!']);
        }
        $codes = Promo::where('code', $code)->first();
        if(is_null($codes)) {
            return response()->json(['success' => false, 'text' => 'Данный промо код не найден']);
        }
        if($codes->steamid64 == Auth::user()->steamid64) {
            return response()->json(['success' => false, 'text' => 'Вы не можете активировать свой промо код']);
        }
        $game = $this->checkGame(Auth::user()->steamid64,730);
        if(!$game) {
            return response()->json(['success' => false, 'text' => 'Профиль скрыт или CS:GO не найдена']);
        }

        Auth::user()->promo = $codes->code;
        Auth::user()->promo_owner = $codes->steamid64;
        Auth::user()->money = Auth::user()->money + $codes->money;
        Auth::user()->save();

        $promo = User::where('steamid64', Auth::user()->promo_owner)->first();
        $promo->money = $promo->money + 10;
        $promo->save();

        return response()->json(['success' => true, 'text' => 'Вы успешно активировали промо код']);
    }

    public function create(Request $request) {
        $code = $request->get('code');
        if(!$request->has('code')) {
            return response()->json(['success' => false, 'text' => 'Невозможно создать данный промо код!']);
        }
        $codes = Promo::where('code', $code)->first();
        if(!is_null($codes)) {
            return response()->json(['success' => false, 'text' => 'Данный промо код уже занят. Попробуйте придумать другой']);
        }
        $promo = Promo::where('steamid64', Auth::user()->steamid64)->first();
        if(is_null($promo)) {
            Promo::create([
                'steamid64' => Auth::user()->steamid64,
                'code' => $code,
                'money' => 25,
                'type' => 0
            ]);
        } else {
            $promo->code = $code;
            $promo->save();
        }
        return response()->json(['success' => true, 'text' => 'Промо код '.$code.' успешно создан. Поделитесь им с друзьями, чтобы они получили 10 рублей на свой баланс']);
    }    
}
