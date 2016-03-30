<?php

namespace App\Http\Controllers;

use App\Bet;
use App\Game;
use App\Promo;
use App\Item;
use App\Lottery;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{

    public function about()
    {
        return view('pages.about');
    }
    public function support()
    {
        return view('pages.support');
    }
    public function promoSettings() {
        $code = Promo::where('steamid64', $this->user->steamid64)->first();
        if(is_null($code)) {
            $code = '';
        }
        else {
            $code = $code->code;  
        }
        return view('pages.referal', compact('code'));
    }
    public function promo() {
        $promo = User::where('promo_owner', $this->user->steamid64)->get();
        $referal = [];
        $money = 0;
        foreach($promo as $ref) {
            $bet = Bet::where('user_id', $ref->id)->orderBy('created_at', 'desc')->get();
            $referal[] = $bet;
            if(!is_null($bet))
                $money = $money + $bet->price; 
        }
        return view('pages.promo', compact('referal', 'money'));
    }
    public function lottery() {
        $lottery = Lottery::where('status', 0)->orderBy('id', 'desc')->first();
        if(!is_null($lottery)) {
            $lottery->items = json_decode($lottery->items);
            $players = $lottery->players()->with(['user','lottery'])->get()->sortByDesc('created_at');
        }
        return view('pages.lottery', compact('lottery', 'players'));
    }
    public function giveaway() {
        $lottery = Lottery::where('status', Game::STATUS_FINISHED)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        foreach($lottery as $key => $lot) {
            $lottery[$key]->items = json_decode($lot->items);
        }
        return view('pages.giveaway', compact('lottery'));
    }
    public function top()
    {
        $users = \DB::table('users')
            ->select('users.id',
                'users.username',
                'users.avatar',
                'users.steamid64',
                \DB::raw('SUM(games.price) as top_value'),
                \DB::raw('COUNT(games.id) as wins_count')
            )
            ->join('games', 'games.winner_id', '=', 'users.id')
            ->groupBy('users.id')
            ->orderBy('top_value', 'desc')
            ->limit(50)
            ->get();
        $place = 1;
        $i = 0;
        foreach($users as $u){
            $users[$i]->games_played = count(\DB::table('games')
                ->join('bets', 'games.id', '=', 'bets.game_id')
                ->where('bets.user_id', $u->id)
                ->groupBy('bets.game_id')
                ->select('bets.id')->get());
            $users[$i]->win_rate = round($users[$i]->wins_count / $users[$i]->games_played, 3) * 100;
            $users[$i]->rang = ($i < 19) ? $i+1 : 18;
            $i++;
        }
        return view('pages.top', compact('users', 'place'));
    }

    public function history()
    {
        $games = Game::with(['bets', 'winner'])
            ->where('status', Game::STATUS_FINISHED)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
 
        foreach($games as $key => $game) {
            $items = array();
            $price = array();
            foreach($game->bets as $bet) {
                foreach(json_decode($bet->items) as $item) {
                    $items[] = (array)$item;
                    $price[] = (array)$item->price;
                }
            }
            array_multisort($price, SORT_DESC, $items);
            $games[$key]->game_items = json_encode($items);
            $games[$key]->chance = $this->_getChancesOfGame($game, true);
        }

        return view('pages.history', compact('games'));
    }
    public function profile()
    {
        $games = Game::where('winner_id', $this->user->id)->get();
        $wins = $games->count();
        $gamesPlayed = count(\DB::table('games')
            ->join('bets', 'games.id', '=', 'bets.game_id')
            ->where('bets.user_id', $this->user->id)
            ->groupBy('bets.game_id')
            ->select('bets.id')->get());
        $looses = $gamesPlayed - $wins;
        $win_price = $games->sum('price');
        return view('pages.profile', compact('wins', 'looses', 'win_price'));
    }
    public function settings(Request $request)
    {
        return view('pages.settings');
    }
    private function _getChancesOfGame($game, $is_object = false)
    {
        $chances = [];
        foreach($game->usersChance() as $user){
            if($is_object){
                $chances[] = (object) [
                    'chance' => $this->_getUserChanceOfGame($user, $game),
                    'avatar' => $user->avatar,
                    'steamid64'  => $user->steamid64
                ];

            }else{
                $chances[] = [
                    'chance' => $this->_getUserChanceOfGame($user, $game),
                    'avatar' => $user->avatar,
                    'steamid64'  => $user->steamid64
                ];
            }

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
            
            if ($bet) {
                if($bet == 0)
                    $chance = 0;
                else
                    $chance = round($bet / $game->price, 3) * 100;
            }
        }
        return $chance;
    }
    public function game($gameId)
    {
        if(isset($gameId) && Game::where('status', Game::STATUS_FINISHED)->where('id', $gameId)->count()){
            $game = Game::with(['winner'])->where('status', Game::STATUS_FINISHED)->where('id', $gameId)->first();
            $game->ticket = floor($game->rand_number * ($game->price * 100));
            $bets = $game->bets()->with(['user','game'])->get()->sortByDesc('to');
            $lastBet = Bet::where('game_id', $gameId)->orderBy('created_at', 'desc')->first();
            $chances = [];
            return view('pages.game', compact('game', 'bets', 'chances', 'lastBet'));
        }
        return redirect()->route('index');
    }
}
