<?php

namespace App\Http\Controllers;

use App\Item;
use App\Bet;
use App\Services\SteamItem;
use App\Services\BackPack;
use App\Services\CsgoFast;
use App\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Invisnik\LaravelSteamAuth\SteamAuth;
use App\Http\Requests;
use App\User;
use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    const SECRET_KEY    = 'oDWx4GYTr4Acbdms';
    
    const NEW_ITEMS_CHANNEL = 'items.to.sale';
    const GIVE_ITEMS_CHANNEL = 'items.to.give';

    const PRICE_PERCENT_TO_SALE = 90;   // Процент от цены steam
    const LINK_TO_BOT_INVENTORY = 'https://steamcommunity.com/profiles/76561198282404874/inventory/#730';
    const LINK_TO_REVIEWS = '';

    private $steamAuth;
    public function __construct(SteamAuth $auth)
    {
        parent::__construct();
        $this->steamAuth = $auth;
    }

    public function index()
    {
        return view('pages.new_shop');
    }

    public function history()
    {
        $items = Shop::where('buyer_id', $this->user->id)->orderBy('buy_at', 'desc')->get();
        return view('pages.historyShop', compact('items'));
    }
    public function setItemStatus(Request $request)
    {
        $item = Shop::find($request->get('id'));
        if(!is_null($item)){
            $status = $request->get('status');
            $item->status = $status;
            if($status == Shop::ITEM_STATUS_ERROR_TO_SEND) {
                $buyer = User::where('id', $item->buyer_id)->first();
                $buyer->money = $buyer->money + $item->price;
                $buyer->save();
                $item->status = Shop::ITEM_STATUS_FOR_SALE;
                $item->buyer_id = null;
            }
            $item->save();
            if($status == Shop::ITEM_STATUS_NOT_FOUND) {
                $buyer = User::where('id', $item->buyer_id)->first();
                $buyer->money = $buyer->money + $item->price;
                $buyer->save();
                $item->delete();
            }
            return $item;
        }
        return response()->json(['success' => false]);
    }

    public function addItemsToSale()
    {
        $jsonItems = $this->redis->lrange(self::NEW_ITEMS_CHANNEL, 0, -1);
        foreach($jsonItems as $jsonItem){
            $items = json_decode($jsonItem, true);
            foreach($items as $item) {
                if ($item['inventoryId']==0)
                    continue;
                $dbItem = Item::where('market_hash_name', $item['market_hash_name'])->first();
                $item['price'] = 0;
                $item['steam_price'] = 0;
                if(!is_null($dbItem)){
                    $item['price'] = round($dbItem->price * config('shop.pricePercentToSell'),2);
                    $item['steam_price'] = $dbItem->price;
                } else {
                    continue;
                }
                if ($item['price'] <= 15) {
                    continue;
                }
                if(empty($item['quality'])) {
                    $item['quality'] = 'Normal';
                }
                $shopItem = Shop::where('inventoryId',$item['inventoryId'])->first();
                if (is_null($shopItem)) {
                    Shop::create($item);
                } else {
                    $shopItem->price = $item['price'];
                    $shopItem->steam_price = $item['steam_price'];
                    $shopItem->save();
                }
            }
            $this->redis->lrem(self::NEW_ITEMS_CHANNEL, 1, $jsonItem);
        }
        return response()->json(['success' => true]);
    }

    public function buyItem(Request $request)
    {
        $item = Shop::find($request->get('id'));
        if(!is_null($item)){
            if(empty($this->user->accessToken)) {
                return response()->json(['success' => false, 'msg' => 'Вы не ввели ссыклу на обмен!']);
            }
            $gamesCount = Bet::where('user_id', $this->user->id)->count();
            if($gamesCount < 5) {
                return response()->json(['success' => false, 'msg' => 'Вы должны сделать 5 депозитов на сайте!']);
            }
            if($item->status > 0) {
                return response()->json(['success' => false, 'msg' => 'Предмет уже куплен!']);
            }
            if($item->status != Shop::ITEM_STATUS_SOLD) {
                if($this->user->money >= $item->price) {
                    if($item->price <= 15) {
                        $this->steamAuth->steamId = $this->user->steamid64;
                        $steamInfo = $this->steamAuth->parseInfo();
                        $steamInfo = $this->steamAuth->getUserInfo();

                        $this->user->username = $steamInfo->getNick();
                        $this->user->save();

                        if(stripos($this->user->username, 'shurzgbets.com') === false) {
                            return response()->json(['success' => false, 'msg' => 'Чтобы покупать предметы дешевле 15 рублей, добавьте в свой ник домен нашего сайта - shurzgbets.com']);
                        }
                    }
                    $item->status = Shop::ITEM_STATUS_SOLD;
                    $item->buyer_id = $this->user->id;
                    $item->buy_at = Carbon::now();
                    $item->save();
                    $this->sendItem($item);
                    $this->user->money = $this->user->money - $item->price;
                    $this->user->save();
                    return response()->json(['success' => true, 'msg' => 'Вы успешно купили предмет! Вы получите его в течении 5 минут.']);
                } else {
                    return response()->json(['success' => false, 'msg' => 'У вас недостаточно средств для покупки.']);
                }
            } else {
                return response()->json(['success' => false, 'msg' => 'Предмет уже куплен!']);
            }
        }else{
            return response()->json(['success' => false, 'msg' => 'Ошибка! Предмет не найден!']);
        }
    }

    public function sendItem($item)
    {
        $value = [
            'id' => $item->id,
            'itemId' => $item->inventoryId,
            'partnerSteamId' => $this->user->steamid64,
            'accessToken' => $this->user->accessToken,
        ];

        $this->redis->rpush(self::GIVE_ITEMS_CHANNEL, json_encode($value));
    }
}
