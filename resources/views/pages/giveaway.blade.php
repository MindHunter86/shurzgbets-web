

@extends('layout')

@section('content')
<div class="rulet_bg">
    <div class="rulet_title"><b>История </b>раздач</div>
    <div class="table">
        <div class="table_panel">
            <div class="t1">#</div>
            <div class="t2">Предмет</div>
            <div class="t3">Победитель</div>
            <div class="t4">Участники</div>
        </div>
        @foreach($lottery as $key => $lot)
            <div class="table_info">
                <div class="t1">{{ $key +1 }}</div>
                <div class="t2">
                    <em class="tava"><img src="https://steamcommunity-a.akamaihd.net/economy/image/class/730/{{$lot->items->classid}}/200fx200f" alt="" /></em>
                    <em class="tname ell">{{$lot->items->market_hash_name}}</em>
                </div>
                <div class="t3">{{ $lot->winner->username }}</div>
                <div class="t4">{{ $lot->players }} / {{ $lot->max }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection