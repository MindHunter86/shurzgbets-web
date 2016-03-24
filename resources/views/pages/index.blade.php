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
<div class="rules rulet_bg">Участвующие вносят скины, по достижении определенного максимального количества случайным образом выбирается победитель, который получит все скины. Шанс выигрыша зависит от стоимости внесенных скинов.</div>
<ul class="stats rulet_bg">
    <li><em>Сегодня игр</em> <b class="stats-gamesToday">{{ \App\Game::gamesToday() }}</b></li>
    <li><em>Сегодня игроков</em> <b class="stats-uniqueUsers">{{ \App\Game::usersToday() }}</b></li>
    <li><em>Макс. выигрыш сегодня:</em> <b class="stats-wintoday">{{ \App\Game::maxPriceToday() }}</b> руб.</li>
    <li><em>Максимальный выигрыш:</em> <b>{{ \App\Game::maxPrice() }}</b> руб.</li>
</ul>
<div class="gamebg">
    <div class="game game_stats" style="display:block;">
        <div class="game_panel">
            <div class="game_num left">Игра #{{ $game->id }}</div>
            <div class="game_cash right"><em>Вступай в игру сейчас и выиграй:</em> <b class="game_bank">{{ round($game->price) }}</b> <span>руб.</span></div>
        </div>
        <div class="game_scale left"><div class="progress game_bar" style="width:{{ $game->items }}%;"></div></div>
        <div class="game_timer right gameEndTimer">
            <span class="min countMinutes">00</span>
            <span class="sec countSeconds">00</span>
        </div>
        <div class="clear"></div>
        <div class="game_info"><em>Мин. ставка {{ $min_price = \App\Http\Controllers\GameController::MIN_PRICE }} руб., максимум предметов {{ $max_items = \App\Http\Controllers\GameController::MAX_ITEMS }}.</em></div>
        <div class="game_info game_info_last"><em>Чем выше ваша ставка, тем выше шанс на победу.</em></div>
        <div class="game_button"><a href="/deposit" class="depositModal" target="_blank">Внести депозит</a></div>
    </div>
    <div class="rul game_winner" style="display:none;">
        <div class="game_panel">
            <div class="game_num left">Игра #{{ $game->id }}</div>
            <div class="game_cash right"><em>Банк игры:</em> <b class="game_bank">{{ round($game->price) }}</b> <span>руб.</span></div>
        </div>
        <div class="rulet">
            <ul class="all-players-list">

            </ul>
        </div>
        <div class="left">
            <div class="rulet_num">Победный билет: <em class="win_ticket"></em></div>
            <div class="rulet_win ell">Победитель: <em class="win_username"></em></div>
        </div>
        <div class="right">
            <div class="rulet_timer">
                <div class="ngtimer">
                    <span class="countSeconds"><span class="position"><span class="digit static">0</span></span><span class="position"><span class="digit static">0</span></span></span>
                </div>
                <em>Новая игра через:</em>
            </div>
            <div class="rulet_button"><a href="/deposit" class="depositModal" target="_blank">ВНЕСТИ ДЕПОЗИТ ПЕРВЫМ</a></div>
        </div>
    </div>
</div>
    <div class="gamestart">
        <em class="gamestart_title">ИГРА НАЧАЛАСЬ! ВНОСИТЕ ДЕПОЗИТ!</em>
        <em class="gamestart_bg">ЧЕСТНАЯ ИГРА</em>
        <em class="gamestart_hash">Хэш раунда: <span>2193dbd3d54e2659c700465fb860cc57645bfb21495c477f162da75a</span></em>
    </div>
@endsection