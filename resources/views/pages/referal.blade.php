@extends('layout')

@section('content')
<div class="rulet_bg">
    <div class="rulet_title"><b>РЕФЕРАЛЬНАЯ</b> СИСТЕМА</div>
    <div class="ref">
        <div class="ref_text">Введите код для получения 15 рублей, которые вы сможете потратить <br />в магазине:</div>
        <input class="ref_code promo-accept-text" type="text" placeholder="Введите код" value="{{ $u->promo }}"/>
        <div class="ref_button"><a href="#" class="accept-promo">АКТИВИРОВАТЬ</a></div>
        <div class="ref_text2">
            <b>РЕФЕРАЛЬНЫЙ КОД</b>
            <em>Вы можете создать свой реферальный код для друзей.</em>
            <input class="ref_code promo-create-text" type="text" value="{{ $code }}" />
            <div class="ref_button"><a href="#" class="create-promo">СОЗДАТЬ</a></div>
        </div>
        <div class="ref_text2">
            <b>РЕФЕРАЛЬНАЯ ССЫЛКА</b>
            <em>Когда кто-то переходит по вашей ссылке и регистрируется, он становится рефералом, так же, как если <br />бы он ввёл ваш код.</em>
            <input class="ref_link" type="text" placeholder="https://shurzgbets.com/r/" />
        </div>
    </div>
</div>
@endsection