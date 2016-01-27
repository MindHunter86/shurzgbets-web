<?php

namespace App\Http\Controllers;

use App\Bet;
use App\Game;
use App\Item;
use App\Lottery;
use App\Referer;
use App\Services\SteamItem;
use App\Services\BackPack;
use App\Ticket;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GameController extends Controller
{
    const SECRET_KEY    = 'oDWx4GYTr4Acbdms';
    const BOT_TRADE_LINK    = 'https://steamcommunity.com/tradeoffer/new/?partner=205485135&token=0hWdC0LX';

    const MIN_PRICE     = 5;                    # Минимальная ставка
    const MAX_ITEMS     = 20;                   # Максимальное кол-во предметов в ставке
    const COMMISSION    = 10;                   # Комиссия
    const COMMISSION_FOR_FIRST_PLAYER    = 7;   # Комиссия для первого игрока сделавшего ставку.
    const APPID         = 730;                  # AppID игры: 570 - Dota2, 730 - CS:GO

    const SEND_OFFERS_LIST = 'send.offers.list';
    const NEW_BET_CHANNEL = 'newDeposit';
    const BET_DECLINE_CHANNEL = 'depositDecline';
    const INFO_CHANNEL = 'msgChannel';
    const SHOW_WINNERS = 'show.winners';
    const ADD_LOTTERY_ITEMS = 'lottery.additems';

    public $redis;
    public $game;
    public $comission;

    private static $chances_cache = [];

    public function __construct()
    {
        parent::__construct();
        $this->game = $this->getLastGame();
        $this->redis->set('current.game', $this->game->id);
    }

    public function deposit()
    {
        return redirect(self::BOT_TRADE_LINK);
    }

    public function currentGame()
    {
        Referer::referer();
        //$lottery = Lottery::orderBy('id', 'desc')->first();
        //$lottery->items = json_decode($lottery->items);

        $game = Game::orderBy('id', 'desc')->first();
        $bets = $game->bets()->with(['user','game'])->get()->sortByDesc('created_at');
        $user_chance = $this->_getUserChanceOfGame($this->user, $game);
        if(!is_null($this->user))
            $user_items = $this->user->itemsCountByGame($game);
        return view('pages.index', compact('game', 'bets', 'user_chance', 'user_items', 'lottery'));
    }

    public function getLastGame()
    {
        $game = Game::orderBy('id', 'desc')->first();
        if(is_null($game)) $game = $this->newGame();
        return $game;
    }

    public function getCurrentGame()
    {
        $this->game->winner;
        return $this->game;
    }

    public function getWinners()
    {
        $us = $this->game->users();

        $lastBet = Bet::where('game_id', $this->game->id)->orderBy('to', 'desc')->first();
        $winTicket = round($this->game->rand_number * $lastBet->to);

        $winningBet = Bet::where('game_id', $this->game->id)->where('from', '<=', $winTicket)->where('to', '>=', $winTicket)->first();

        $this->game->winner_id      = $winningBet->user_id;
        $this->game->status         = Game::STATUS_FINISHED;
        $this->game->finished_at    = Carbon::now();
        $this->game->won_items      = json_encode($this->sendItems($this->game->bets, $this->game->winner));
        $this->game->comission      = $this->comission;
        $this->game->save();

        $returnValue = [
            'game'   => $this->game,
            'winner' => $this->game->winner,
            'round_number' => $this->game->rand_number,
            'ticket' => $winTicket,
            'tickets' => ($this->game->price * 100),
            'users' => $us,
            'chance' => $this->_getUserChanceOfGame($this->game->winner, $this->game)
        ];

        return response()->json($returnValue);
    }

    public function sendItems($bets, $user) {
        $itemsInfo = [];
        $items = [];
        $commission = self::COMMISSION;
        $commissionItems = [];
        $returnItems = [];
        $tempPrice = 0;
        //$firstBet = Bet::where('game_id', $this->game->id)->orderBy('created_at', 'asc')->first();
        //if($firstBet->user == $user) $commission = self::COMMISSION_FOR_FIRST_PLAYER;
        $commissionPrice = round(($this->game->price / 100) * $commission);
        foreach($bets as $bet){
            $betItems = json_decode($bet->items, true);
            foreach($betItems as $item){
                    //(Отдавать всю ставку игроку обратно)
                if($bet->user == $user) {
                    $itemsInfo[] = $item;
                    if(isset($item['classid'])) {
                        if($item['classid'] != "1111111111")
                            $returnItems[] = $item['classid'];
                    }else{
                        $user->money = $user->money + $item['price'];
                    }
                }else {
                    $items[] = $item;
                }
            }
        }

        foreach($items as $item){
            if($item['price'] < 1) $item['price'] = 1;
            if(($item['price'] >= 10) && ($tempPrice+$item['price'] < $commissionPrice)) {
            //if(($item['price'] <= $commissionPrice) && ($tempPrice < $commissionPrice) && ($item['price'] >= 10)){
                $commissionItems[] = $item;
                $tempPrice = $tempPrice + $item['price'];
            } else{
                $itemsInfo[] = $item;
                if(isset($item['classid'])) {
                    if($item['classid'] != "1111111111")
                        $returnItems[] = $item['classid'];
                }else{
                    $user->money = $user->money + $item['price'];
                }
            }
        }
        $this->comission = $tempPrice;

        $user->save();

        $value = [
            'appId' => self::APPID,
            'steamid' => $user->steamid64,
            'accessToken' => $user->accessToken,
            'items' => $returnItems,
            'game' => $this->game->id
        ];
        if(count($returnItems) > 0) {
            $this->redis->rpush(self::SEND_OFFERS_LIST, json_encode($value));
        }
        if(count($commissionItems) > 0) {
            $this->redis->rpush(self::ADD_LOTTERY_ITEMS, json_encode($commissionItems));
        }
        return $itemsInfo;
    }

    public function newGame()
    {
        $rand_number = "0.".mt_rand(0,9).mt_rand(10000000,99999999).mt_rand(100000000,999999999);
        $game = Game::create(['rand_number' => $rand_number]);
        //$game->rand_number = "0.".mt_rand(100000000,999999999).mt_rand(100000000,999999999);
        $game->hash = md5($rand_number);
        $game->today = Game::gamesToday();
        $game->userstoday = Game::usersToday();
        $game->maxwin = Game::maxPriceToday();
        $this->redis->set('current.game', $game->id);
        return $game;
    }

    public function checkOffer()
    {
        $data = $this->redis->lrange('check.list', 0, -1);
        foreach($data as $offerJson) {
            $offer = json_decode($offerJson);
            $accountID = $offer->accountid;
            $items = json_decode($offer->items, true);
            $itemsCount = count($items);

            $user = User::where('steamid64', $accountID)->first();
            if (is_null($user)) {
                $this->redis->lrem('usersQueue.list', 1, $accountID);
                $this->redis->lrem('check.list', 0, $offerJson);
                $this->redis->rpush('decline.list', $offer->offerid);
                continue;
            }
            $totalItems = $user->itemsCountByGame($this->game);
            if ($itemsCount > self::MAX_ITEMS /*|| $totalItems > self::MAX_ITEMS || ($itemsCount+$totalItems) > self::MAX_ITEMS*/) {
                $this->_responseErrorToSite('Максимальное кол-во предметов для депозита - ' . self::MAX_ITEMS, $accountID, self::BET_DECLINE_CHANNEL);
                $this->redis->lrem('usersQueue.list', 1, $accountID);
                $this->redis->lrem('check.list', 0, $offerJson);
                $this->redis->rpush('decline.list', $offer->offerid);
                continue;
            }

            $total_price = $this->_parseItems($items, $missing, $price);

            if ($missing) {
                $this->_responseErrorToSite('Принимаются только предметы из CS:GO', $accountID, self::BET_DECLINE_CHANNEL);
                $this->redis->lrem('usersQueue.list', 1, $accountID);
                $this->redis->lrem('check.list', 0, $offerJson);
                $this->redis->rpush('decline.list', $offer->offerid);
                continue;
            }

            if ($price) {
                $this->_responseErrorToSite('Невозможно определить цену одного из предметов', $accountID, self::BET_DECLINE_CHANNEL);
                $this->redis->lrem('usersQueue.list', 1, $accountID);
                $this->redis->lrem('check.list', 0, $offerJson);
                $this->redis->rpush('decline.list', $offer->offerid);
                continue;
            }

            if ($total_price < self::MIN_PRICE) {
                $this->_responseErrorToSite('Минимальная сумма депозита ' . self::MIN_PRICE . 'р.', $accountID, self::BET_DECLINE_CHANNEL);
                $this->redis->lrem('usersQueue.list', 1, $accountID);
                $this->redis->lrem('check.list', 0, $offerJson);
                $this->redis->rpush('decline.list', $offer->offerid);
                continue;
            }

            $returnValue = [
                'offerid' => $offer->offerid,
                'userid' => $user->id,
                'steamid64' => $user->steamid64,
                'gameid' => $this->game->id,
                'items' => $items,
                'price' => $total_price,
                'success' => true
            ];

            if ($this->game->status == Game::STATUS_PRE_FINISH || $this->game->status == Game::STATUS_FINISHED) {
                $this->_responseMessageToSite('Ваша ставка пойдёт на следующую игру.', $accountID);
                $returnValue['gameid'] = $returnValue['gameid'] + 1;
            }

            $this->redis->rpush('checked.list', json_encode($returnValue));
            $this->redis->lrem('check.list', 0, $offerJson);
        }
        return response()->json(['success' => true]);
    }

    public function newBet()
    {
        $data = $this->redis->lrange('bets.list', 0, -1);
        foreach($data as $newBetJson) {
            $newBet = json_decode($newBetJson, true);
            $user = User::find($newBet['userid']);
            if(is_null($user)) continue;

            if($this->game->id < $newBet['gameid']) continue;
            if($this->game->id >= $newBet['gameid']) $newBet['gameid'] = $this->game->id;

            if ($this->game->status == Game::STATUS_PRE_FINISH || $this->game->status == Game::STATUS_FINISHED) {
                $this->_responseMessageToSite('Ваша ставка пойдёт на следующую игру.', $user->steamid64);
                $this->redis->lrem('bets.list', 0, $newBetJson);
                $newBet['gameid'] = $newBet['gameid'] + 1;
                $this->redis->rpush('bets.list', json_encode($newBet));
                continue;
            }

            $lastBet = Bet::where('game_id', $this->game->id)->orderBy('to', 'desc')->first();

            $bonus = round(($newBet['price']/100) * 10);

            if (is_null($lastBet)) {
                $newBet['price'] = $newBet['price'] + $bonus;
                $newBet['items'][] = array("price" => $bonus, "classid" => "1111111111", "rarity" => "milspec", "name" => "Бонус за первый депозит", "img"=> "/new/images/bonus-dark.png");
            }

            $ticketFrom = 0;
            $ticketTo = ($newBet['price'] * 100);
            if (!is_null($lastBet)) {
                $ticketFrom = $lastBet->to + 1;
                $ticketTo = $ticketFrom + ($newBet['price'] * 100) - 1;
            }
            $bet = new Bet();
            $bet->user()->associate($user);
            $bet->items = json_encode($newBet['items']);
            $bet->itemsCount = count($newBet['items']);
            $bet->price = $newBet['price'];
            $bet->from = $ticketFrom;
            $bet->to = $ticketTo;
            $bet->game()->associate($this->game);
            $bet->save();

            $bets = Bet::where('game_id', $this->game->id);
            $this->game->items = $bets->sum('itemsCount');
            $this->game->price = $bets->sum('price');

            if (count($this->game->users()) >= 2 || $this->game->items >= 100) {
                $this->game->status = Game::STATUS_PLAYING;
                $this->game->started_at = Carbon::now();
            }
            if ($this->game->items >= 100) {
                $this->game->status = Game::STATUS_FINISHED;
                $this->redis->publish(self::SHOW_WINNERS, true);
            }

            $this->game->save();

            $chances = $this->_getChancesOfGame($this->game);
            $returnValue = [
                'betId' => $bet->id,
                'userId' => $user->steam64,
                'html' => view('includes.bet', compact('bet'))->render(),
                'itemsCount' => $this->game->items,
                'gamePrice' => $this->game->price,
                'gameStatus' => $this->game->status,
                'chances' => $chances
            ];
            $this->redis->publish(self::NEW_BET_CHANNEL, json_encode($returnValue));
            $this->redis->lrem('bets.list', 0, $newBetJson);
        }
        return $this->_responseSuccess();
    }

    public function addTicket(Request $request)
    {
        if(!$request->has('id')) return response()->json(['text' => 'Ошибка. Попробуйте обновить страницу.', 'type' => 'error']);
        if($this->game->status == Game::STATUS_PRE_FINISH || $this->game->status == Game::STATUS_FINISHED) return response()->json(['text' => 'Дождитесь следующей игры!', 'type' => 'error']);
        $id = $request->get('id');
        $ticket = Ticket::find($id);
        if(is_null($ticket)) return response()->json(['text' => 'Ошибка.', 'type' => 'error']);
        else {
            if ($this->user->money >= $ticket->price) {

                $this->user->money = $this->user->money - $ticket->price;
                $this->user->save();
                
                $lastBet = Bet::where('game_id', $this->game->id)->orderBy('to', 'desc')->first();

                $ticketFrom = 0;
                if (!is_null($lastBet)) $ticketFrom = $lastBet->to + 1;

                $bet = new Bet();
                $bet->user()->associate($this->user);
                $bet->items = json_encode([$ticket]);
                $bet->itemsCount = 1;
                $bet->price = $ticket->price;
                $bet->from = $ticketFrom;
                $bet->to = $bet->from + ($ticket->price * 100);
                $bet->game()->associate($this->game);
                $bet->save();

                $bets = Bet::where('game_id', $this->game->id);
                $this->game->items = $bets->sum('itemsCount');
                $this->game->price = $bets->sum('price');

                if (count($this->game->users()) >= 2) {
                    $this->game->status = Game::STATUS_PLAYING;
                    $this->game->started_at = Carbon::now();
                }

                if($this->game->items >= 100){
                    $this->game->status = Game::STATUS_FINISHED;
                    $this->redis->publish(self::SHOW_WINNERS,true);
                }

                $this->game->save();

                $chances = $this->_getChancesOfGame($this->game);

                $returnValue = [
                    'betId' => $bet->id,
                    'userId' => $this->user->steamid64,
                    'html' => view('includes.bet', compact('bet'))->render(),
                    'itemsCount' => $this->game->items,
                    'gamePrice' => $this->game->price,
                    'gameStatus' => $this->game->status,
                    'chances' => $chances
                ];
                $this->redis->publish(self::NEW_BET_CHANNEL, json_encode($returnValue));
                return response()->json(['text' => 'Действие выполнено.', 'type' => 'success']);
            }else{
                return response()->json(['text' => 'Недостаточно средств на вашем балансе.', 'type' => 'error']);
            }
        }
    }

    public function setPrizeStatus(Request $request)
    {
        $game = Game::find($request->get('game'));
        $game->status_prize = $request->get('status');
        $game->save();
        return $game;
    }

    public function setGameStatus(Request $request)
    {
        $this->game->status = $request->get('status');
        $this->game->save();
        return $this->game;
    }

    public function userqueue(Request $request)
    {
        $user = User::where('steamid64', $request->get('id'))->first();
        if(!is_null($user)) {
            return response()->json([
                'username' => $user->username,
                'avatar' => $user->avatar
            ]);   
        }
        return response('Error. User not found.', 404);
    }

    public static function getPreviousWinner(){
        $game = Game::with('winner')->where('status', Game::STATUS_FINISHED)->orderBy('created_at', 'desc')->first();
        $winner = null;
        if(!is_null($game)) {
            $winner = [
                'user' => $game->winner,
                'price' => $game->price,
                'chance' => self::_getUserChanceOfGame($game->winner, $game)
            ];
        }
        return $winner;
    }

    public function getBalance(){
        return $this->user->money;
    }

    private function _getChancesOfGame($game)
    {
        $chances = [];
        foreach($game->users() as $user){
            $chances[] = [
                'chance' => $this->_getUserChanceOfGame($user, $game),
                'items' => User::find($user->id)->itemsCountByGame($game),
                'steamid64'  => $user->steamid64
            ];
        }
        return $chances;
    }

    public static function _getUserChanceOfGame($user, $game)
    {
        $chance = 0;
        if (!is_null($user)) {
            //if(isset(self::$chances_cache[$user->id])) return self::$chances_cache[$user->id];
            $bet = Bet::where('game_id', $game->id)
                ->where('user_id', $user->id)
                ->sum('price');
            if ($bet)
                $chance = round($bet / $game->price, 3) * 100;
            self::$chances_cache[$user->id] = $chance;
        }
        return $chance;
    }

    private function _parseItems(&$items, &$missing = false, &$price = false)
    {
        $itemInfo = [];
        $total_price = 0;
        $i = 0;

        foreach ($items as $item) {
            $value = $item['classid'];
            if($item['appid'] != GameController::APPID) {
                $missing = true;
                return;
            }
            $dbItemInfo = Item::where('market_hash_name', $item['market_hash_name'])->first();
            if(is_null($dbItemInfo)){
                if(!isset($itemInfo[$item['classid']]))
                    $itemInfo[$value] = new BackPack($item);

                if(empty($itemInfo[$item['classid']]->name))
                    $itemInfo[$item['classid']]->name = "";
                
                $dbItemInfo = Item::create((array)$itemInfo[$item['classid']]);

                if (!$itemInfo[$value]->price) $price = true;
            }else{
                if($dbItemInfo->updated_at->getTimestamp() < Carbon::now()->subHours(5)->getTimestamp()) {
                    $si = new BackPack($item);
                    if (!$si->price) $price = true;
                    $dbItemInfo->price = $si->price;
                    $dbItemInfo->save();
                }
            }

            $itemInfo[$value] = $dbItemInfo;

            if(!isset($itemInfo[$value]))
                $itemInfo[$value] = new BackPack($item);
            if (!$itemInfo[$value]->price) $price = true;
            if($itemInfo[$value]->price < 1) $itemInfo[$value]->price = 1;          //Если цена меньше единицы, ставим единицу
            $total_price = $total_price + $itemInfo[$value]->price;
            $items[$i]['price'] = $itemInfo[$value]->price;
            unset($items[$i]['appid']);
            $i++;
        }
        return $total_price;
    }

    private function _responseErrorToSite($message, $user, $channel)
    {
        return $this->redis->publish($channel, json_encode([
            'user' => $user,
            'msg' => $message
        ]));
    }
    private function _responseMessageToSite($message, $user)
    {
        return $this->redis->publish(self::INFO_CHANNEL, json_encode([
            'user' => $user,
            'msg' => $message
        ]));
    }


    private function _responseSuccess()
    {
        return response()->json(['success' => true]);
    }

}
