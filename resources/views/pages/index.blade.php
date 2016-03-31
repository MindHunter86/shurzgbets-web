@extends('layout')

@section('content')
@if(!Auth::guest())
    @if(empty($u->accessToken))    
    <div class="notrade rulet_bg notradehide">
        <div class="notrade_text"><b>Внимание!</b> Необходимо ввести ссылку на обмен. <a href="http://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url">Где взять ссылку?</a></div>
        <input class="notrade_link trade_link" type="text" placeholder="" />
        <div class="notrade_text2">Обязательно убедитесь что ваш инвентарь публично доступен в Steam для получения приза!</div>
        <div class="notrade_button"><a href="#" class="save-link2">Запомнить ссылку</a></div>
    </div>
    @endif
@endif
<div class="rules rulet_bg">{{ trans('all.info') }}</div>
<ul class="stats rulet_bg">
    <li><em>{{ trans('all.stats.games') }}</em> <b class="stats-gamesToday">{{ \App\Game::gamesToday() }}</b></li>
    <li><em>{{ trans('all.stats.players') }}</em> <b class="stats-uniqueUsers">{{ \App\Game::usersToday() }}</b></li>
    <li><em>{{ trans('all.stats.max_win_day') }}</em> <b class="stats-wintoday">{{ \App\Game::maxPriceToday() }}</b> {{ trans('all.valute') }}</li>
    <li><em>{{ trans('all.stats.max_win') }}</em> <b>{{ \App\Game::maxPrice() }}</b> {{ trans('all.valute') }}</li>
</ul>
<div class="gamebg">
    <div class="game game_stats" style="display:block;">
        <div class="game_panel">
            <div class="game_num left">{{ trans('all.game.game_id') }} #<span>{{ $game->id }}</span></div>
            <div class="game_cash right"><em>{{ trans('all.game.game_invite') }}</em> <b class="game_bank">{{ round($game->price) }}</b> <span>{{ trans('all.valute') }}</span></div>
        </div>
        <div class="game_scale left"><div class="progress game_bar" style="width:{{ $game->items }}%;"></div></div>
        <div class="game_timer right gameEndTimer">
            <span class="min countMinutes">00</span>
            <span class="sec countSeconds">00</span>
        </div>
        <div class="clear"></div>
        <div class="game_info"><em>{{ trans('all.game.min_deposit') }} {{ $min_price = \App\Http\Controllers\GameController::MIN_PRICE }} {{ trans('all.valute') }}, {{ trans('all.game.max_items') }} {{ $max_items = \App\Http\Controllers\GameController::MAX_ITEMS }}.</em></div>
        <div class="game_info game_info_last"><em>{{ trans('all.game.chance_info') }}</em></div>
        <div class="game_button"><a href="/deposit" class="depositModal" target="_blank">{{ trans('all.game.deposit') }}</a></div>
    </div>
    <div class="rul game_winner" style="display:none;">
        <div class="game_panel">
            <div class="game_num left">{{ trans('all.game.game_id') }} #{{ $game->id }}</div>
            <div class="game_cash right"><em>{{ trans('all.game.game_bank') }}</em> <b class="game_bank">{{ round($game->price) }}</b> <span>{{ trans('all.valute') }}</span></div>
        </div>
        <div class="rulet">
            <ul class="all-players-list">

            </ul>
        </div>
        <div class="left">
            <div class="rulet_num">{{ trans('all.game.win_ticket') }} <em class="win_ticket"></em></div>
            <div class="rulet_win ell">{{ trans('all.game.win_players') }} <em class="win_username"></em></div>
        </div>
        <div class="right">
            <div class="rulet_timer">
                <div class="ngtimer">
                    <span class="countSeconds"><span class="position"><span class="digit static">0</span></span><span class="position"><span class="digit static">0</span></span></span>
                </div>
                <em>{{ trans('all.game.new_game') }}</em>
            </div>
            <div class="rulet_button"><a href="/deposit" class="depositModal" target="_blank">{{ trans('all.game.deposit_one') }}</a></div>
        </div>
    </div>
</div>
<div class="cart">
    <div class="cart_text">
        <em>{{ trans('all.card.info') }}</em>
        <span>{{ trans('all.card.small_info') }}</span>
    </div>
    <div class="cart_loop">
        <div class="cart_info" onclick="addTicket(1);"><em>25</em></div>
        <div class="cart_info" onclick="addTicket(2);"><em>50</em></div>
        <div class="cart_info" onclick="addTicket(3);"><em>150</em></div>
        <div class="cart_info" onclick="addTicket(4);"><em>300</em></div>
        <div class="cart_info" onclick="addTicket(5);"><em>600</em></div>
        <div class="cart_info" onclick="addTicket(6);"><em>1000</em></div>
    </div>
</div>
<div class="chance" @if(count($percents) == 0) style="display:none;" @endif>
    <ul class="chances">
    @foreach($percents as $p)
        <li><b>{{ $p->chance }}%</b><img src="{{ $p->avatar }}" alt="" /></li>
    @endforeach
    </ul>
</div>
<div class="gamestart game_round_number" style="display:none;">
    <em class="gamestart_title">{{ trans('all.hash.end') }}</em>
    <em class="gamestart_bg">{{ trans('all.hash.fair') }}</em>
    <em class="gamestart_hash">{{ trans('all.hash.hash') }} <span class="round_number"></span></em>
</div>
<div class="allitems_loop game_items">
    @foreach($bets as $bet)
        @include('includes.bet')
    @endforeach
</div>
<div class="gamestart game_hash">
    <em class="gamestart_title">{{ trans('all.hash.start') }}</em>
    <em class="gamestart_bg">{{ trans('all.hash.fair') }}</em>
    <em class="gamestart_hash">{{ trans('all.hash.hash') }} <span class="game_hash_number">{{ md5($game->rand_number) }}</span></em>
</div>
@endsection