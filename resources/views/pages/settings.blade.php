@extends('layout')

@section('content')

<div class="rulet_bg">
    <div class="rulet_title"><b>НАСТРОЙКИ</b></div>
    <div class="notrade rulet_bg">
        <div class="notrade_text">Необходимо ввести ссылку на обмен. <a href="http://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url">Где взять ссылку?</a></div>
        <input class="notrade_link trade_link" type="text" value="{{ $u->trade_link }}" />
        <div class="notrade_text2">Обязательно убедитесь что ваш инвентарь публично доступен в Steam для получения приза!</div>
        <div class="notrade_button"><a href="#" class="save-link2">Запомнить ссылку</a></div>
    </div>
</div>
@endsection