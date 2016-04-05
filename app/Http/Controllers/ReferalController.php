<?php

namespace App\Http\Controllers;

use App\User;
use App\Promo;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


class ReferalController extends Controller {
    public function accept(Request $request) {
        $code = $request->get('code');
        if(!$request->has('code')) {
            return response()->json(['success' => false, 'text' => 'Невозможно активировать данный промо код!']);
        }
        if(!empty(strlen(Auth::user()->promo))) {
            return response()->json(['success' => false, 'text' => 'Вы уже активировали промо код']);
        }
        $codes = Promo::where('code', $code)->first();
        if(is_null($codes)) {
            return response()->json(['success' => false, 'text' => 'Данный промо код не найден']);
        }
        if($codes->steamid64 == Auth::user()->steamid64) {
            return response()->json(['success' => false, 'text' => 'Вы не можете активировать свой промо код']);
        }
        $game = simplexml_load_file('http://steamcommunity.com/profiles/'.Auth::user()->steamid64.'/games?tab=all&xml=1'); 
        $game = json_decode(json_encode($game), true);
        $game = $game['games']['game'];
        $game = array_column($game, 'appID');
        if(!$game) {
            return response()->json(['success' => false, 'text' => 'Профиль скрыт или CS:GO не найдена']);
        }
        $game = array_search(730, $game);
        if($game === false) {
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
