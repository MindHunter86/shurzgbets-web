@extends('layout')

@section('content')
<div class="content_bg">
    <div class="full">
        <div class="content_title"><div>История <b>раздач</b></div></div>
        <div class="clear"></div>
        <div class="inv_table">
            <div class="inv_table_panel">
                <div class="type1">Предмет</div>
                <div class="type2">Победитель</div>
                <div class="type3">Статус</div>
            </div>
        </div>
        @foreach($lottery as $lot)
            <div class="inv_table_info">
                <div class="type1">
                    <div><img src="https://steamcommunity-a.akamaihd.net/economy/image/class/730/{{$lottery->items->classid}}/200fx200f"/></div>
                    {{$lottery->items->market_hash_name}}
                </div>
                <div class="type2"><a href="#" data-profile="{{ $lottery->user->steamid64 }}">{{ $lottery->user->username }}</a></div>
                <div class="type3">
                    <span style="color:green;">Завершен</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection