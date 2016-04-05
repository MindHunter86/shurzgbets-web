@extends('layout')

@section('content')
<div class="rulet_bg">
    <div class="rulet_title"><b>Топ</b> игроков</div>
    <div class="table">
        <div class="table_panel">
            <div class="t1">Место</div>
            <div class="t2">Ник в Steam</div>
            <div class="t3">Кол-во побед</div>
            <div class="t4">Выигрыш</div>
        </div>
        @foreach($users as $key => $user)
            @if($user->steamid64 != '76561198254647128')
            <div class="table_info">
                <div class="t1">{{ $key+1 }}</div>
                <div class="t2">
                    <em class="tava"><img src="{{ $user->avatar }}" alt="" /></em>
                    <em class="trang"><img src="/shurzg/images/rang/{{ $user->rang }}.png" alt="" /></em>
                    <em class="tname ell">{{ $user->username }}</em>
                </div>
                <div class="t3">{{ $user->wins_count }}</div>
                <div class="t4">{{ round($user->top_value) }} руб.</div>
            </div>
            @endif
        @endforeach
    </div>
</div>
@endsection