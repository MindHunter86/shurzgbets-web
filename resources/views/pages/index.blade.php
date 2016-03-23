@extends('layout')

@section('content')
    <div class="container_r">
        @if(!Auth::guest())
            @if(empty($u->accessToken))    
            <div class="notrade rulet_bg">
                <div class="notrade_text"><b>Внимание!</b> Необходимо ввести ссылку на обмен. <a href="http://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url">Где взять ссылку?</a></div>
                <input class="notrade_link" type="text" placeholder="" />
                <div class="notrade_text2">Обязательно убедитесь что ваш инвентарь публично доступен в Steam для получения приза!</div>
                <div class="notrade_button"><a href="#">Запомнить ссылку</a></div>
            </div>
            @endif
        @endif
        <div class="rules rulet_bg">Участвующие вносят скины, по достижении определенного максимального количества случайным образом выбирается победитель, который получит все скины. Шанс выигрыша зависит от стоимости внесенных скинов.</div>
        <ul class="stats rulet_bg">
            <li><em>Сегодня игр</em> <b class="stats-gamesToday">{{ \App\Game::gamesToday() }}</b></li>
            <li><em>Сегодня игроков</em> <b class="stats-uniqueUsers">{{ \App\Game::usersToday() }}</b></li>
            <li><em>Максимальный выигрыш сегодня:</em> <b class="stats-wintoday">{{ \App\Game::maxPriceToday() }}</b> руб.</li>
            <li><em>Максимальный выигрыш:</em> <b>{{ \App\Game::maxPrice() }}</b> руб.</li>
        </ul>
    </div>
@endsection