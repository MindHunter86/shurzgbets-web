@extends('layout')

@section('content')

<div class="rulet_bg">
    <div class="rulet_title">{!! trans('setting.title') !!}</div>
    <div class="notrade rulet_bg">
        <div class="notrade_text">{{ trans('setting.setting.title') }} <a href="http://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url">{{ trans('setting.setting.why') }}</a></div>
        <input class="notrade_link trade_link" type="text" value="{{ $u->trade_link }}" />
        <div class="notrade_text2">{{ trans('setting.setting.help') }}</div>
        <div class="notrade_button"><a href="#" class="save-link2">{{ trans('setting.setting.save') }}</a></div>
    </div>
</div>
@endsection