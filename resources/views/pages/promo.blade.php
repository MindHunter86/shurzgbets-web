@extends('layout')

@section('content')
<div class="rulet_bg">
    <div class="rulet_title"><b>Реферальная </b>статистика (Всего заработано: {{ $money }} руб.)</div>
    <div class="table">
        <div class="table_panel">
            <div class="t1"></div>
            <div class="t2">Реферал</div>
            <div class="t3">Код</div>
            <div class="t4">Начисления</div>
        </div>
        @foreach($promo as $key => $pro)
        <div class="table_info">
            <div class="t1">{{$key + 1 }}</div>
            <div class="t2">{{ $pro->username }}</div>
            <div class="t3">{{ $pro->promo }}</div>
            <div class="t4">15 руб.</div>
        </div>
        @endforeach
    </div>
</div>
@endsection