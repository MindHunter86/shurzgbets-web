

@extends('layout')

@section('content')
<div class="rulet_bg">
    <div class="rulet_title">{!! trans('giveaway.history.title') !!}</div>
    <div class="table">
        <div class="table_panel">
            <div class="t1">{{ trans('giveaway.history.table.t1') }}</div>
            <div class="t2">{{ trans('giveaway.history.table.t2') }}</div>
            <div class="t3">{{ trans('giveaway.history.table.t3') }}</div>
            <div class="t4">{{ trans('giveaway.history.table.t4') }}</div>
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