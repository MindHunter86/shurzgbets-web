<?php

namespace App\Http\Controllers;

use App\User;
use App\Promo;
use App\Item;
use App\ReferalTransaction;
use Auth;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


class ReferalController extends Controller {
    const STATUS_NOT_SENDED = 0;
    const STATUS_WAIT = 1;
    const STATUS_ERROR = 4;
    const STATUS_SENDED = 3;
    const STATUS_ITEMS_NOT_FOUND = 2;

    const REF_ERROR_CHANNEL = 'depositDecline';
    const REF_INFO_CHANNEL = 'infoMsg';


    const REF_CHANNEL = 'refitems.to.send';

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
        if(!$game && !Auth::user()->is_admin) {
            return response()->json(['success' => false, 'text' => 'Профиль скрыт или CS:GO не найдена']);
        }

        Auth::user()->promo = $codes->code;
        Auth::user()->promo_owner = $codes->steamid64;
        //Auth::user()->money = Auth::user()->money + $codes->money;
        Auth::user()->save();
        $this->sendItems(Auth::user());

        $promo = User::where('steamid64', Auth::user()->promo_owner)->first();
        $promo->money = $promo->money + 10;
        $promo->save();

        return response()->json(['success' => true, 'text' => 'Вы успешно активировали промо код']);
    }

    private function sendItems($userAuth) {
        //$itemsToSend = config('referal.rewardItems');
        $itemsToSend = [];

        DB::beginTransaction();
        $user = User::where('id',$userAuth->id)->lockForUpdate()->first();
        $sendInfo = [
            "userid" => $user->id,
            'partnerSteamId' => $user->steamid64,
            'accessToken' => $user->accessToken,
            "items" => $itemsToSend
        ];
        if ($user->promo_status == self::STATUS_SENDED) {
            //Вещи уже отправлены
            return response()->json(['success' => false, 'text' => 'Награда уже была отправлена!']);
        }
        if ($user->promo_status != self::STATUS_WAIT) {
            if ($this->redis->llen('referal_cache_list')>=2) {
                array_push($itemsToSend,json_decode($this->redis->lpop('referal_cache_list')));
                array_push($itemsToSend,json_decode($this->redis->rpop('referal_cache_list')));
            }
            $user->promo_status = self::STATUS_WAIT;
            $user->save();
            DB::commit();
            $this->redis->rpush(self::REF_CHANNEL, json_encode($sendInfo));
            return response()->json(['success' => true, 'text' => 'Запрос на отправку создан!']);
        } else {
            return response()->json(['success' => false, 'text' => 'Запрос уже обрабатывается!']);
        }
    }

    public function send(Request $request) {
        $user = Auth::user();
        return $this->sendItems($user);
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

    public function updateStatus(Request $request)
    {
        $userid = $request->get('userid');
        $status = $request->get('status');
        $user = User::find($userid);
        if (!is_null($user)) {
            $user->promo_status = $status;
            $user->save();
            $accountID = $user->steamid64;
            switch($status) {
                case self::STATUS_ERROR:
                    $this->_responseMsgToSite('При отправке возникла ошибка, попрбуйте позже!', $accountID, self::REF_ERROR_CHANNEL);
                    break;
                case self::STATUS_ITEMS_NOT_FOUND:
                    $this->_responseMsgToSite('Бонусные предметы временно отсутствуют на боте, попрбуйте позже!', $accountID, self::REF_ERROR_CHANNEL);
                    break;
                case self::STATUS_NOT_SENDED:
                    break;
                case self::STATUS_SENDED:
                    $this->_responseMsgToSite('Бонусные предметы отправлены!', $accountID, self::REF_INFO_CHANNEL);
                    $items = $request->get('items');
                    $tradeId = $request->get('tradeId');
                    $sum = 0;
                    foreach ($items as $item) {
                        $item['market_hash_name'] = str_replace('{StatTrak}','StatTrak™',$item['market_hash_name']);
                        $sum += $item['price'];
                    }
                    $statistics = new ReferalTransaction();
                    $statistics->referal_items = json_encode($items);
                    $statistics->tradeId = $tradeId;
                    $statistics->gainer_id = $userid;
                    $statistics->total_price = $sum;
                    $statistics->save();
                    break;
            }
        }
    }

    public function updateItemsCache(Request $request) {
        $items = $request->get('items');
        $refItems = [];
        foreach ($items as $item) {
            $item['market_hash_name'] = str_replace('{StatTrak}','StatTrak™',$item['market_hash_name']);
            $dbItem = Item::where('market_hash_name',$item['market_hash_name'])->first();
            if (!is_null($dbItem)) {
                $refitem = [
                    'market_hash_name' => $item['market_hash_name'],
                    'assetId' => $item['assetId'],
                    'price' => $dbItem->price
                ];
                array_push($refItems,$refitem);
            }
        }
        usort($refItems, function ($a, $b) {
            return $a['price'] - $b['price'];
        });
        $this->redis->del('referal_cache_list');
        foreach ($refItems as $item)
            $this->redis->rpush('referal_cache_list', json_encode($item));
        $this->redis->set('ref_cache_update', 0);
        return;
    }

    private function _responseMsgToSite($message, $user, $channel)
    {
        return $this->redis->publish($channel, json_encode([
            'user' => $user,
            'msg' => $message
        ]));
    }
}
