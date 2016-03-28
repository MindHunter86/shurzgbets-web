
@extends('layout')

@section('content')
<div class="rulet_bg">
    <div class="rulet_title"><b>Реферальная </b>статистика (Всего заработано: {{ $money }} руб.)</div>
    <div class="table">
        <div class="table_panel">
            <div class="t1"></div>
            <div class="t2">Реферал</div>
            <div class="t3">Ставка</div>
            <div class="t4">Ваш доход</div>
        </div>
        @foreach($referal as $refa)
            @foreach($refa as $key => $ref)
            <div class="table_info">
                <div class="t2">{{ $ref->user->username }}</div>
                <div class="t3">{{ $ref->price }} руб.</div>
                <div class="t4">{{ round(($ref->price / 100) * 1)  }} руб.</div>
            </div>
            @endforeach
        @endforeach
    </div>
</div>
@endsection