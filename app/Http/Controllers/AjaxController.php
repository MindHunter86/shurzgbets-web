<?php

namespace App\Http\Controllers;

use App\Game;
use App\Shop;
use App\User;
use App\Item;
use App\Bet;
use Firebase\Firebase;
use App\Services\BackPack;
use App\Services\CsgoFast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Carbon\Carbon;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use PhpParser\Node\Expr\Cast\Object_;

class AjaxController extends Controller
{
    public function chat(Request $request) {
        $type = $request->get('type');
        if(!$request->has('type')) {
            return response()->json(['success' => false, 'text' => 'Тип запроса не указан']);
        }
        if($type == 'push') {
            if (!is_null($this->user->chat_banned) && $this->user->chat_banned->timestamp>time()) {
                $banTime = $this->user->chat_banned->format('d.m.Y G:i T');
                return response()->json(['success' => false, 'text' => 'Вы не можете отправлять сообщения, так как забанены до '.$banTime]);
            }
            $censure = array('залупа', '.ru', '.com', '. ru', 'ru', '.in', '. com', 'заходи', 'классный сайт', 'го на');
            $message = $request->get('message');
            if(is_null($message)) {
                return response()->json(['success' => false, 'text' => 'Вы не ввели сообщение']);
            }
            if(strlen($message) == 0) {
                return response()->json(['success' => false, 'text' => 'Вы не ввели сообщение']);
            }
            if(strlen($message) > 200) {
                return response()->json(['success' => false, 'text' => 'Максимум 200 символов']);
            }
            if (Session::has('last_chat_message') && Session::get('last_chat_message')+1>time()) {
                return response()->json(['success' => false, 'text' => '1 сообщение в секунду']);
            }
            $gamesCount = Bet::where('user_id', $this->user->id)->count();
            if($gamesCount < 5 && !$this->user->is_admin) {
                return response()->json(['success' => false, 'text' => 'Вы должны сделать хотябы 5 депозитов на сайте!']);
            }
            $message = str_replace($censure, '***', $message);
            $push = array(
                'username' => $this->user->username,
                'avatar' => $this->user->avatar,
                'steamid' => $this->user->steamid64,
                'is_admin' => $this->user->is_admin,
                'is_moderator' => $this->user->is_moderator,
                'is_vip'    => $this->user->is_vip,
                'message' => $message
            );
            $this->redis->publish('chat_new_message', json_encode([
                'username' => $this->user->username,
                'text' => $message,
                'avatar' => $this->user->avatar,
                'steamid' => $this->user->steamid64
            ]));
            Session::set('last_chat_message',time());
            return response()->json(['success' => true, 'text' => 'Сообщение добавлено']);
        }
        if($type == 'remove') {
            if(!$this->user->is_moderator && !$this->user->is_admin) {
                return response()->json(['success' => false, 'text' => 'Вам недоступная данная функция!']);
            }
            $id = $request->get('id');
            $this->redis->publish('chat_remove_message', json_encode([
                'id' => $id
            ]));
            return response()->json(['success' => true, 'text' => 'Сообщение удалено']);
        }
        if ($type == 'ban') {
            if(!$this->user->is_moderator && !$this->user->is_admin) {
                return response()->json(['success' => false, 'text' => 'Вам недоступная данная функция!']);
            }
            $steamid = $request->get('steamid');
            $time = $request->get('time');
            if ($time == -1)
                $time = 60*24*365*10;
            $banTime = Carbon::now()->addMinutes($time);
            $user = User::where('steamid64',$steamid)->first();
            if ($user) {
                $user->chat_banned = $banTime;
                $user->save();
                $this->redis->publish('chat_ban', json_encode([
                    'steamid' => $steamid,
                    'bantime' => $banTime->format('d.m.Y G:i T')
                ]));
            return response()->json(['success' => true, 'text' => 'Пользователь забанен в чате на '.$time.' минут']);
            }
        }
    }
    //
    public function parseAction(Request $request)
    {
        switch($request->get('action')){
            case 'myinventory':
                $jsonInventory = file_get_contents('http://steamcommunity.com/profiles/' . $this->user->steamid64 . '/inventory/json/730/2');
                $items = json_decode($jsonInventory, true);
                if ($items['success']) {
                    foreach ($items['rgDescriptions'] as $class_instance => $item) {
                        $info = Item::where('market_hash_name', $item['market_hash_name'])->first();
                        if (is_null($info)) {
                            $info = new CsgoFast($item);
                            if ($info->price != null) {
                                //Item::create((array)$info);
                            }
                            else {
                                $info->price = 0;
                            }
                        }
                        $items['rgDescriptions'][$class_instance]['price'] = $info->price;
                    }

                }
                return response()->json($items);
                break;
            case 'gameInfo': 
                $game = Game::orderBy('id', 'desc')->take(1)->first();
                return $game;
            case 'userInfo':
                $user = User::where('steamid64', $request->get('id'))->first();
                if(!is_null($user)) {
                    $games = Game::where('winner_id', $user->id)->get();
                    $wins = $games->count();
                    $gamesPlayed = \DB::table('games')
                        ->join('bets', 'games.id', '=', 'bets.game_id')
                        ->where('bets.user_id', $user->id)
                        ->groupBy('bets.game_id')
                        ->orderBy('games.created_at', 'desc')
                        ->select('games.*', \DB::raw('SUM(bets.price) as betValue'))->get();
                    $gamesList = [];
                    $i = 0;
                    foreach ($gamesPlayed as $game) {
                        $gamesList[$i] = (object)[];
                        $gamesList[$i]->id = $game->id;
                        $gamesList[$i]->win = false;
                        $gamesList[$i]->bank = $game->price;
                        if ($game->winner_id == $user->id) $gamesList[$i]->win = true;
                        if ($game->status != Game::STATUS_FINISHED) $gamesList[$i]->win = -1;
                        $gamesList[$i]->chance = round($game->betValue / $game->price, 3) * 100;
                        $i++;
                    }
                    return response()->json([
                        'username' => $user->username,
                        'avatar' => $user->avatar,
                        'votes' => $user->votes,
                        'wins' => $wins,
                        'url' => 'http://steamcommunity.com/profiles/' . $user->steamid64 . '/',
                        'winrate' => count($gamesPlayed) ? round($wins / count($gamesPlayed), 3) * 100 : 0,
                        'totalBank' => $games->sum('price'),
                        'games' => count($gamesPlayed),
                        'list' => $gamesList
                    ]);
                }
                return response('Error. User not found.', 404);
                break;
            case 'voteUser':
                $user = User::where('steamid64', $request->get('id'))->first();
                if(!is_null($user)) {
                    if($user->id == $this->user->id)
                        return response()->json([
                            'status' => 'error',
                            'msg' => 'Вы не можете голосовать за себя.'
                        ]);
                    $votes = $this->redis->lrange($user->steamid64 . '.user.votes.list', 0, -1);
                    if(in_array($this->user->id, $votes)){
                        return response()->json([
                            'status' => 'error',
                            'msg' => 'Вы уже голосовали за этого пользователя.'
                        ]);
                    }else{
                        $user->votes++;
                        $user->save();
                        $this->redis->rpush($user->steamid64 . '.user.votes.list', $this->user->id);
                        return response()->json([
                            'status' => 'success',
                            'votes' => $user->votes
                        ]);
                    }
                }
                return response('Error. User not found.', 404);
                break;
            case 'shopSort':
                $options = $request->get('options');
                if(empty($options['searchRarity'])) $options['searchRarity'] = [ "Тайное", "Classified", "Restricted", "Industrial Grade", "Mil-Spec Grade", "Consumer Grade", "High Grade", "Base Grade", "Exotic", "Covert"];
                if(empty($options['searchQuality'])) $options['searchQuality'] = [ "Factory new", "Minimal Wear", "Field-Tested", "Well-Worn", "Battle-Scarred", "Normal", "StatTrak™", "Souvenir", "Not Painted"];
                if(empty($options['searchType'])) $options['searchType'] = [ "Pistol", "SMG", "Rifle", "Shotgun", "Sniper Rifle", "Machinegun", "Container", "Knife", "Sticker", "Music Kit", "Key", "Pass", "Gift", "Tag", "Tool"];
                //if(empty($options['searchType'])) $options['searchType'] = [ "Knife", "Винтовка", "Дробовик", "Pistol", "Снайперская винтовка", "Пулемёт", "Контейнер", "Пистолет-пулемёт", "Sitcket", "Набор музыки", "Ключ", "Подарок"];

                $items = Shop::where('name', 'like', '%'.$options['searchName'].'%')
                    ->whereBetween('price', [$options['minPrice'], $options['maxPrice'] + 1])
                    ->whereIn('type', $options['searchType'])
                    ->whereIn('rarity', $options['searchRarity'])
                    ->whereIn('quality', $options['searchQuality'])
                    ->orderBy('price', $options['sort'])
                    ->where('status', Shop::ITEM_STATUS_FOR_SALE)
                    ->get();
                return response()->json($items->toArray());
                break;
            case 'getnews':
                $lastUserNews = Session::has('readed_news') ? Session::get('readed_news') : 0;
                $lastNews = json_decode($this->redis->get('site_news'));
                if (!is_null($lastNews) && $lastNews->time>$lastUserNews) {
                    $lastNews->success = true;
                    return response()->json($lastNews);
                }
                return response()->json(['success'=>false]);
                break;
            case 'newsreaded':
                Session::set('readed_news',time());
                return response()->json(['type'=>'success']);
                break;
        }
    }
}
