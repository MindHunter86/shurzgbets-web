<?php

namespace App\Http\Controllers;

use App\Bet;
use App\Game;
use App\Order;
use App\Shop;
use App\ReferalTransaction;
use App\Item;
use App\Services\SteamItem;
use App\User;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

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
    public function history()
    {
        $games = Game::with(['bets', 'winner'])
            ->where('status', Game::STATUS_FINISHED)
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        return view('admin.history', compact('games'));
    }

    public function hashes() {
        $games = Game::orderBy('created_at', 'desc')
            ->paginate(50);
        return view('admin.hashes', compact('games'));
    }

    public function game($gameId)
    {
        if(isset($gameId) && Game::where('status', Game::STATUS_FINISHED)->where('id', $gameId)->count()){
            $game = Game::with(['winner'])->where('status', Game::STATUS_FINISHED)->where('id', $gameId)->first();
            $game->ticket = floor($game->rand_number * ($game->price * 100));
            $bets = $game->bets()->with(['user','game'])->get()->sortByDesc('to');
            $lastBet = Bet::where('game_id', $gameId)->orderBy('created_at', 'desc')->first();
            $chances = [];
            return view('admin.game', compact('game', 'bets', 'chances', 'lastBet'));
        }
        return redirect()->route('index');
    }

    public function referalStat()
    {
        $transactions = ReferalTransaction::orderBy('sended_at', 'desc')
            ->paginate(50);
        $itemCount = $this->redis->llen('referal_cache_list');
        return view('admin.refstat', compact('transactions','itemCount'));
    }

    public function settings()
    {
        $stakeDecline = $this->redis->get('auto_decline_stakes');
        $news = $this->redis->get('site_news');
        if (is_null($news)) {
            $news = json_decode('{"header": "", "message": ""}');
        } else {
            $news = json_decode($news);
        }
        return view('admin.settings', compact('stakeDecline','news'));
    }

    public function ajaxNews(Request $request) {
        $type = $request->get('type');
        switch ($type) {
            case 'add':
                $news = [
                    'header' => $request->get('header'),
                    'message' => $request->get('message'),
                    'time' => time()
                ];
                $newsJson = json_encode($news);
                $this->redis->set('site_news',$newsJson);
                $this->redis->publish('news_update',$newsJson);
                return response()->json(['type' => 'success']);
                break;
            case 'remove':
                $this->redis->del('site_news');
                return response()->json(['type' => 'success']);
                break;
        }

        return response()->json(['text' => 'Неизвестная команда', 'type' => 'error']);
    }

    public function ajaxStakes(Request $request) {
        $type = $request->get('type');
        switch ($type) {
            case 'on':
                $this->redis->set('auto_decline_stakes',0);
                return response()->json(['type' => 'success']);
                break;
            case 'off':
                $this->redis->set('auto_decline_stakes',1);
                return response()->json(['type' => 'success']);
                break;
        }
        return response()->json(['text' => 'Неизвестная команда', 'type' => 'error']);
    }

    public function updateItemsCache() {
        if ($this->redis->get('ref_cache_update') == 1) {
            return response()->json(['text' => 'В данный момент обновление кэша реферальных вещей уже ведется!', 'type' => 'error']);
        }
        $this->redis->set('ref_cache_update', 1);
        $this->redis->rpush('newReferalItems', true);
        return response()->json(['text' => 'Начато обновление кэша.', 'type' => 'success']);

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
        $winner = $game->winner;
        if(trim($winner->accessToken)==false) {
            return response()->json(['text' => 'У победителя игры #'.$game->id.' не введена ссылка на обмен!', 'type' => 'error']);
        }
    	$this->sendItems($game, $game->winner);
    	return response()->json(['type' => 'success']);
    }

    public function sendAllAjax(Request $request) {
        $games = Game::where('status_prize',Game::STATUS_PRIZE_SEND_ERROR)->whereDate('finished_at', '>', Carbon::now()->subDay())->get();
        foreach ($games as $game) {
            if ($game->status_prize == Game::STATUS_PRIZE_WAIT_TO_SENT)
                continue;
            $winner = $game->winner;
            if (trim($winner->accessToken) == false) {
                continue;
            }
            $this->sendItems($game, $game->winner);
        }
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

    public function sendItems($game, $user) {
        $itemsInfo = [];
        $returnItems = [];
        $wonItems = json_decode($game->won_items, true);
        foreach($wonItems as $item){
                $itemsInfo[] = $item;
                if(isset($item['classid'])) {
                    if($item['classid'] != "1111111111")
                        $returnItems[] = [
                            'assetId' => $item['assetId'],
                            'classid' => $item['classid']
                        ];
                }
        }

        $value = [
            'appId' => self::APPID,
            'steamid' => $user->steamid64,
            'accessToken' => trim($user->accessToken),
            'items' => $returnItems,
            'game' => $game->id,
            'resend' => true
        ];

        $this->redis->rpush(self::SEND_OFFERS_LIST, json_encode($value));
        $game->status_prize = 0;
        $game->save();
        return $itemsInfo;
    }
}