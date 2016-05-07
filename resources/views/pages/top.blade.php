@extends('layout')

@section('content')
<div class="rulet_bg">
    <div class="rulet_title">{!! trans('top.title') !!}</div>
    <div class="table">
        <div class="table_panel">
            <div class="t1">{{ trans('top.table.t1') }}</div>
            <div class="t2">{{ trans('top.table.t2') }}</div>
            <div class="t3">{{ trans('top.table.t3') }}</div>
            <div class="t4">{{ trans('top.table.t4') }}</div>
        </div>
        @foreach($users as $key => $user)
            <div class="table_info">
                <div class="t1">{{ $key+1 }}</div>
                <div class="t2">
                    <em class="tava"><img src="{{ $user->avatar }}" alt="" /></em>
                    <em class="trang"><img src="/shurzg/images/rang/{{ $user->rang }}.png" alt="" /></em>
                    <em class="tname ell">{{ $user->username }}</em>
                </div>
                <div class="t3">{{ $user->wins_count }}</div>
                <div class="t4">{{ round($user->top_value) }} {{ trans('all.valute') }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection
