<?php

namespace App\Http\Controllers;

use App\Item;
use App\Services\SteamItem;
use App\Services\BackPack;
use App\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Invisnik\LaravelSteamAuth\SteamAuth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    const SECRET_KEY    = 'oDWx4GYTr4Acbdms';
    
    const NEW_ITEMS_CHANNEL = 'items.to.sale';
    const GIVE_ITEMS_CHANNEL = 'items.to.give';

    const PRICE_PERCENT_TO_SALE = 70;   // Процент от цены steam
    const LINK_TO_BOT_INVENTORY = 'https://steamcommunity.com/profiles/76561198038766700/inventory/#730';
    const LINK_TO_REVIEWS = '';

    private $steamAuth;
    public function __construct(SteamAuth $auth)
    {
        parent::__construct();
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

    public function admin()
    {
        $items = Shop::whereNotNull('buyer_id')->orderBy('buy_at', 'desc')->get();
        return view('pages.adminShop', compact('items'));
    }

    public function setItemStatus(Request $request)
    {
        $item = Shop::find($request->get('id'));
        if(!is_null($item)){
            $item->status = $request->get('status');
            $item->save();
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
                //$dbItemInfo = Item::where('market_hash_name', $item['market_hash_name'])->first();
                //if (is_null($dbItemInfo)) {
                    $itemInfo = new BackPack($item);
                    $item['steam_price'] = $itemInfo->price;
                    $item['price'] = round($item['steam_price']/100 * self::PRICE_PERCENT_TO_SALE);
                    Shop::create($item);
                /*}else{
                    $item['steam_price'] = $dbItemInfo->price;
                    $item['price'] = round($item['steam_price']/100 * self::PRICE_PERCENT_TO_SALE);
                    Shop::create($item);
                }*/
            }
            $this->redis->lrem(self::NEW_ITEMS_CHANNEL, 1, $jsonItem);
        }
        return response()->json(['success' => true]);
    }

    public function buyItem(Request $request)
    {
        $item = Shop::find($request->get('id'));
        if(!is_null($item)){
            if($item->status == Shop::ITEM_STATUS_SOLD) return response()->json(['success' => false, 'msg' => 'Предмет уже куплен!']);
            if($this->user->money >= $item->price){
                if($item->price <= 15) {
                    $this->steamAuth->steamId = $this->user->steamid64;
                    $steamInfo = $this->steamAuth->parseInfo();
                    $steamInfo = $this->getUserInfo();
                    
                    $this->user->username = $steamInfo->getNick();
                    $this->user->save();

                    if(stripos($this->user->username, 'itemup.ru') === false) {
                        return response()->json(['success' => false, 'msg' => 'Чтобы покупать предметы дешевле 15 рублей, добавьте в свой ник домен нашего сайта - itemup.ru']);
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
            }else{
                return response()->json(['success' => false, 'msg' => 'У вас недостаточно средств для покупки.']);
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
