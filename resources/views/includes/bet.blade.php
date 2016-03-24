@foreach(json_decode($bet->items) as $i)
<div class="allitems {{ $i->rarity }}">
  <div class="allitems_ava"><img src="{{ $bet->user->avatar }}" alt="" /></div>
  <div class="allitems_in">
    <div class="allitems_name ell">{{ $bet->user->username }}</div>
    <div class="allitems_cash ell">вложил <b>{{ $i->name }}</b> (~{{ $i->price }} руб.)</div>
    <div class="allitems_num">Билеты: от #{{ $bet->from }} до #{{ $bet->to }}</div>
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