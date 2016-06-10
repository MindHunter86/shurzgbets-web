@extends('layout')

@section('content')
<div class="rulet_bg">
    <div class="rulet_title"><b>РЕФЕРАЛЬНАЯ</b> СИСТЕМА</div>
    <div class="ref">
    @if(!strlen($u->promo))
        <div class="ref_text">Введите код для получения бонусных скинов:</div>
        <input class="ref_code promo-accept-text" type="text" placeholder="Введите код" value="{{ $u->promo }}"/>
        <div class="ref_button"><a href="#" class="accept-promo">АКТИВИРОВАТЬ</a></div>
    @else
        @if ($u->promo_status==3)
        <div class="ref_text">Вы использовали промо-код:</div>
        <input class="ref_code promo-accept-text" type="text" value="{{ $u->promo }}" disabled />
        <div class="ref_text">И получили бонусные скины!</div>
        @else
        <div class="ref_text">Вы использовали промо-код:</div>
        <input class="ref_code promo-accept-text" type="text" value="{{ $u->promo }}" disabled />
        <div class="ref_button"><a href="#" class="send-promo">ОТПРАВИТЬ НАГРАДУ</a></div>
        @endif
    @endif
        <div class="ref_text2">
            <b>РЕФЕРАЛЬНЫЙ КОД</b>
            <em>Вы можете создать свой реферальный код для друзей.</em>
            <input class="ref_code promo-create-text" type="text" value="{{ $code }}" />
            <div class="ref_button"><a href="#" class="create-promo">СОЗДАТЬ</a></div>
        </div>
        <div class="ref_text2">
            <div class="ref_button"><a href="/referals/stats">Реферальная статистика</a></div>
        </div>
        <div class="ref_text2">
        <center>Как это работает?</center>
        <p>На Ваш баланс зачисляется 10 рублей с каждого приглашенного игрока.</p>
        <p>Подробную информацию можно посмотреть в реферальной статистике</p>
        </div>
    </div>
</div>
@endsection