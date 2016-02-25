<?php

namespace App\Http\Controllers;

use App\Bet;
use App\Game;
use App\Order;
use App\Shop;
use App\Item;
use App\Services\SteamItem;
use App\User;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class AdminController extends Controller {
    const MIN_PRICE     = 30;                    # Минимальная ставка
    const MAX_ITEMS     = 20;                   # Максимальное кол-во предметов в ставке
    const COMMISSION    = 10;                   # Комиссия
    const COMMISSION_FOR_FIRST_PLAYER    = 7;   # Комиссия для первого игрока сделавшего ставку.
    const APPID         = 730;                  # AppID игры: 570 - Dota2, 730 - CS:GO

    const SEND_OFFERS_LIST = 'send.offers.list';
    const NEW_BET_CHANNEL = 'newDeposit';
    const BET_DECLINE_CHANNEL = 'depositDecline';
    const INFO_CHANNEL = 'msgChannel';
    const SHOW_WINNERS = 'show.winners';
    const NEW_ITEMS_CHANNEL = 'items.to.sale';
    const GIVE_ITEMS_CHANNEL = 'items.to.give';

    public $redis;

    public function index() {
        $users = User::count();
        $sales = Shop::where('buyer_id', '>', 0)->count();
        $sumPay = Order::where('status', 1)->sum('amount');
        $bot = User::where('steamid64', '0000000000000')->first();
        $botBet = Bet::where('user_id', $bot->id)->get();
        $botSumBet = 0;
        $botGet = [];
        foreach($botBet as $bets) {
            foreach(json_decode($bets->items) as $item) {
                $botSumBet = $botSumBet + $item->price;
            }
        }


        $hourgames = DB::select(DB::raw('select created_at as y, SUM(`comission`) as a from `games` where DAY(created_at) = DAY(NOW()) group by hour(created_at) order by created_at asc;'));
        $hourgames = json_encode((array)$hourgames);
    	$games = DB::select(DB::raw('select DATE(created_at) as y, SUM(`comission`) as item1 from `games` where `created_at` >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) group by DATE(created_at)'));
    	$plays = DB::select(DB::raw('select DATE(created_at) as y, count(created_at) as item1 from `games` where `created_at` >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) group by DATE(created_at)'));

        $average = DB::select(DB::raw('select sum(comission) as average from games where created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)'));
        $average = round($average[0]->average / 30);
        
        $averageGame = DB::select(DB::raw('select count(created_at) as average from games where created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)'));
        $averageGame = round($averageGame[0]->average / 30);

        $referer = DB::select(DB::raw('select * from referer ORDER BY count DESC'));

       	$plays = json_encode($plays);
       	$sumplays = DB::select(DB::raw('select count(created_at) as sum from `games` where `created_at` >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)'));
       	$sumplays = $sumplays[0]->sum;
        $items = [];
        $commission = self::COMMISSION;
        $sum = 0;
        foreach($games as $game) {
        	$sum += $game->item1;
			array_push($items, $game);
        }
        $items = json_encode($items);
        return view('admin.index', compact('sumPay', 'sales', 'users', 'botSumBet','items', 'sum', 'plays', 'sumplays', 'average', 'averageGame', 'referer', 'hourgames'));
    }
    public function send() {
    	return view('admin.send');
    }

    public function shop() {
        $shop = DB::table('shop')
            ->select('shop.*', 'users.username', 'users.trade_link')
            ->join('users', 'shop.buyer_id', '=', 'users.id')
            ->where('buyer_id', '>', '0')
            ->orderBy('buy_at', 'desc')
            ->get(); 
    	return view('admin.shop', compact('shop'));
    }

    public function sendAjax(Request $request) {
    	$game = Game::where('id', $request->get('game'))->first();
    	if($game->status_prize == Game::STATUS_PRIZE_WAIT_TO_SENT) {
    		return response()->json(['text' => 'Приз уже отправляется.', 'type' => 'error']);
    	}
    	$this->sendItems($game, $game->bets, $game->winner);
    	return response()->json(['type' => 'success']);
    }
    public function sendshopAjax(Request $request) {
    	$shop = Shop::find($request->get('buy'));
    	if(!is_null($shop) && isset($shop->buyer_id)) {
	    	$user = User::find($shop->buyer_id);
	    	$value = [
	            'id' => $shop->id,
	            'itemId' => $shop->inventoryId,
	            'partnerSteamId' => $user->steamid64,
	            'accessToken' => $user->accessToken,
	        ];

	        $this->redis->rpush(self::GIVE_ITEMS_CHANNEL, json_encode($value));
	        return response()->json(['type' => 'success']);
    	}
    	return response()->json(['text' => 'Товар еще не продан или отсутствует', 'type' => 'error']);
    }

    public function sendItems($game, $bets, $user) {
        $itemsInfo = [];
        $items = [];
        $commission = self::COMMISSION;
        $commissionItems = [];
        $returnItems = [];
        $tempPrice = 0;
        //$firstBet = Bet::where('game_id', $this->game->id)->orderBy('created_at', 'asc')->first();
        //if($firstBet->user == $user) $commission = self::COMMISSION_FOR_FIRST_PLAYER;
        $commissionPrice = round(($game->price / 100) * $commission);
        foreach($bets as $bet){
            $betItems = json_decode($bet->items, true);
            foreach($betItems as $item){
                    //(Отдавать всю ставку игроку обратно)
                if($bet->user == $user) {
                    $itemsInfo[] = $item;
                    if(isset($item['classid'])) {
                        if($item['classid'] != "1111111111")
                            $returnItems[] = $item['classid'];
                    }
                }else {
                    $items[] = $item;
                }
            }
        }


        foreach($items as $item){
            if($item['price'] < 1) $item['price'] = 1;
            if(($item['price'] >= 5) && ($tempPrice+$item['price'] < $commissionPrice)) {
                if(isset($item['classid'])) {
                    if($item['classid'] != "1111111111") {
                        $commissionItems[] = $item;
                        $tempPrice = $tempPrice + $item['price'];
                    }
                } else {
                    $commissionItems[] = $item;
                    $tempPrice = $tempPrice + $item['price'];
                }
            } else{
                if(isset($item['classid'])) {
                    if($item['classid'] != "1111111111")
                        $returnItems[] = $item['classid'];
                }
            }
        }
        $value = [
            'appId' => self::APPID,
            'steamid' => $user->steamid64,
            'accessToken' => $user->accessToken,
            'items' => $returnItems,
            'game' => $game->id
        ];

        $this->redis->rpush(self::SEND_OFFERS_LIST, json_encode($value));
        return $itemsInfo;
    }
}