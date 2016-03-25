@extends('layout')

@section('content')
<div class="rulet_bg">
	<div class="rulet_title"><b>Розыгрыш</b> предмета</div>
	@if(!is_null($lottery))
	<div class="lottery">
		<div class="lottery_info">
			<div class="lottery_weapon"><img src="https://steamcommunity-a.akamaihd.net/economy/image/class/{{ \App\Http\Controllers\GameController::APPID }}/{{ $lottery->items->classid }}/200fx200f" alt="" /></div>
			<div class="lottery_rub"><b>{{ $lottery->price }}</b> руб.</div>
		</div>
		<div class="lottery_button"><a href="#"><b>ПРИНЯТЬ УЧАСТИЕ</b>в этом розыгрыше</a></div>
		<div class="clear"></div>
		<div class="lottery_text">
			<div class="left">Список участников (<span class='currentPlayer'>{{ $lottery->players }}</span> / <span class='currentMax'>{{ $lottery->max }}</span>):</div>
			<div class="right"><a href="#">История розыгрышей</a></div>
		</div>
		<div class="lottery_loop">
		@foreach($players as $player)
			<img src="{{ $player->user->avatar }}" data-id="{{ $player->user->id }}" alt="" />
		@endforeach
		</div>
	</div>
	@endif
</div>
@endsection