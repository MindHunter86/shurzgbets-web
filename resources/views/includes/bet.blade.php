@foreach(json_decode($bet->items) as $i)
<div class="allitems @if(!isset($i->img)){{ $i->rarity }} @endif animated fadeInDown">
  <div class="allitems_ava"><img src="{{ $bet->user->avatar }}" alt="" /></div>
  <div class="allitems_in">
    <div class="allitems_name ell">{{ $bet->user->username }}</div>
    <div class="allitems_cash ell">{{ trans('all.deposit.deposited') }} <b>{{ $i->name }}</b> (~{{ $i->price }} {{ trans('all.valute') }})</div>
    <div class="allitems_num">{{ trans('all.deposit.ticekts') }} {{ trans('all.deposit.from') }} #{{ $bet->from }} {{ trans('all.deposit.to') }} #{{ $bet->to }}</div>
  </div>
  <div class="allitems_w">
  @if(!isset($i->img))
    <img src="https://steamcommunity-a.akamaihd.net/economy/image/class/{{ \App\Http\Controllers\GameController::APPID }}/{{ $i->classid }}/200fx200f" alt="" />
  @else
    <img src="{{ $i->img }}" alt="" />
  @endif
  </div>
</div>
@endforeach