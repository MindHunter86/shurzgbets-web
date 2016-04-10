@extends('layout')

@section('content')
<div class="rulet_bg">
	<div class="rulet_title">{!! trans('giveaway.title') !!}</div>
	@if(!is_null($lottery))
	<div class="lottery">
		<div class="lottery_info">
			<div class="lottery_weapon"><img class="lotteryImg" src="https://steamcommunity-a.akamaihd.net/economy/image/class/{{ \App\Http\Controllers\GameController::APPID }}/{{ $lottery->items->classid }}/200fx200f" alt="" /></div>
			<div class="lottery_rub"><b class="lotteryPrice">{{ $lottery->price }}</b> {{ trans('all.valute') }}</div>
		</div>
		<div class="lottery_button"><a href="#" class="hoax-button">{{ trans('giveaway.giveaway.accept') }}</a></div>
		<div class="clear"></div>
		<div class="lottery_text">
			<div class="left">{{ trans('giveaway.giveaway.players') }} (<span class='currentPlayer'>{{ $lottery->players }}</span> / <span class='currentMax'>{{ $lottery->max }}</span>):</div>
			<div class="right"><a href="/giveaway/history">{{ trans('giveaway.giveaway.history') }}</a></div>
		</div>
		<div class="lottery_loop list-players">
		@foreach($players as $player)
			<img src="{{ $player->user->avatar }}" data-id="{{ $player->user->id }}" alt="" />
		@endforeach
		</div>
	</div>
	@endif
</div>
@endsection