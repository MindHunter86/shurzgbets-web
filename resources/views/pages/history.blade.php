@extends('layout')

@section('content')
<div class="hist_title"><b>История</b> игр <a href="/history/profile" style="float: right;">Мои победы</a></div>
@forelse($games as $game)
<div class="hist">
    <div class="hist_bg">
        <div class="hist_ava"><img src="{{ $game->winner->avatar }}" alt="" /></div>
        <div class="hist_in">
            <div class="left">
                <div class="hist_name ell">{{ $game->winner->username }}</div>
                <div class="hist_cash">Выигрыш: <em>{{ $game->price }}руб.</em></div>
                <div class="hist_win">Шанс на победу: <span>{{ \App\Http\Controllers\GameController::_getUserChanceOfGame($game->winner, $game) }}%</span></div>
            </div>
            <div class="right hist_num">Игра <b>#{{ $game->id }}</b></div>
        </div>
    </div>
    <div class="hist_chance chance">
        <ul class="chances">
            @foreach($game->chance as $c)
            <li><b>{{ $c->chance }}%</b><img data-toggle="tooltip" data-original-title="{{ $c->username }}" src="{{ $c->avatar }}" alt="" /></li>
            @endforeach
        </ul>
    </div>
    <div class="hist_bg2">
        <em>Выигрыш с учетом комиссии:</em>
        @foreach(json_decode($game->won_items) as $i)
            @if(!isset($i->img))
            <div class="item" data-toggle="tooltip" data-original-title="{{ $i->name }} (~{{ $i->price }} {{ trans('all.valute') }})"><img src="https://steamcommunity-a.akamaihd.net/economy/image/class/{{ \App\Http\Controllers\GameController::APPID }}/{{ $i->classid }}/200fx200f" alt="" /></div>
            @else
            <div class="item" data-toggle="tooltip" data-original-title="{{ $i->name }} (~{{ $i->price }} {{ trans('all.valute') }})"><img src="{{ $i->img }}" alt="" /></div>
            @endif
        @endforeach
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $('.wrapper').tooltip({
            trigger: "hover",
            selector: "div[data-toggle=tooltip]"
        });
        $('.chances').mCustomScrollbar({
            axis:'x',
            theme:'dark-thin',
            autoExpandScrollbar:true,
            advanced:{autoExpandHorizontalScroll:true}
        }); 
    })
</script>
@empty
<center><h1 style="color: #33BDA6;">Игр нет!</h1></center>
@endforelse
@endsection