<?php

namespace App\Http\Controllers;

use Auth;
use App\Game;
use App\Promo;
use App\Smile;
use Carbon\Carbon;
use LRedis;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    public $user;
    public $redis;
    public $title;

    public function __construct()
    {
        $this->setTitle('Title not stated');
        $god = false;
        if(Auth::check())
        {
            $this->user = Auth::user();
            view()->share('u', $this->user);

            $god = $this->isGod();
            /*if($god) {
                $smile = Smile::get();
                view()->share('smiles', $smile);
            }*/
        }
        view()->share('god', $god);
        $this->redis = LRedis::connection();
        view()->share('steam_status', $this->getSteamStatus());

        $game = Game::orderBy('id', 'desc')->take(1)->first();
        if(!is_null($game)) {
            $lastWinner = Game::where('status', Game::STATUS_FINISHED)->orderBy('id', 'desc')->take(1)->first();
            view()->share('lastWinner', $lastWinner);
        }
        $dayLucky = Game::where('created_at', '>=', Carbon::today())->where('status', 3)->orderBy('chance','asc')->orderBy('price', 'desc')->take(1)->first();
        view()->share('dayLucky', $dayLucky);
    }

    public function  __destruct()
    {
        $this->redis->disconnect();
    }

    public function setTitle($title)
    {
        $this->title = $title;
        view()->share('title', $this->title);
    }
    public function isGod() {
        if($this->user->is_admin || $this->user->is_moderator || $this->user->is_vip) {
            return true;
        }
        else {
            return false;
        }
    }
    public function getSteamStatus()
    {
        $inventoryStatus = $this->redis->get('steam.inventory.status');
        $communityStatus = $this->redis->get('steam.community.status');

        if($inventoryStatus == 'normal' && $communityStatus == 'normal') return 'good';
        if($inventoryStatus == 'normal' && $communityStatus == 'delayed') return 'normal';
        if($inventoryStatus == 'critical' || $communityStatus == 'critical') return 'bad';
        return 'good';
    }
}
