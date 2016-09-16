<!DOCTYPE html>
<html>
<head>
    <title>SHURZGBETS.COM</title>
    <meta charset="utf-8">
    <meta property="og:title" content="SHURZGBETS.COM" />
    <meta name="keywords" content="Рулетка cs go для бомжей с минимальной ставкой 1 рубль. Именно рулетки кс го с минимальной ставкой 1 рубль самые доступные для бомжей. SHURZGBETS.COM - это cs go рулетка не больше 100 рублей без минимальной ставки. " />
    <meta name="description" content="Рулетка cs go для бомжей с минимальной ставкой 1 рубль. Именно рулетки кс го с минимальной ставкой 1 рубль самые доступные для бомжей. SHURZGBETS.COM - это cs go рулетка не больше 100 рублей без минимальной ставки. " />
    <meta name="csrf-token" content="{!!  csrf_token()   !!}">
    <link type="text/css" rel="stylesheet" href="{{ asset('shurzg/css/style.css') }}" />
    <link type="text/css" rel="stylesheet" href="{{ asset('shurzg/css/animate.css') }}" />
    <link type="text/css" rel="stylesheet" href="{{ asset('shurzg/css/jquery.mCustomScrollbar.css') }}" />
    <link rel="shortcut icon" href="{{ asset('shurzg/images/favicon.ico') }}" />
    <script type="text/javascript" src="{{ asset('new/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/jquery.cookie.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/jquery.smoothscroll.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/jquery.arcticmodal-0.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/jquery.ikSelect.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/script.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/notify.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/countdown.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/bootstrap-tooltip.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}" ></script>

    <script>
        var CHAT_CONNECT = '/chat/4';
        var SITE_URL = '{{url('/')}}';
        var DEPOSIT_URL  = '{{url('/deposit')}}';
    </script>

    @if(!Auth::guest())
        <script>
            const USER_ID = '{{ $u->steamid64 }}';
            var START = true;
            var role = {
                'admin' : {{ $u->is_admin ? 'true' : 'false' }},
                'moderator' : {{ $u->is_moderator ? 'true' : 'false' }}
            };
        </script>
    @else
        <script>
            const USER_ID = '0';
            var START = true;
            var role = {
                'admin' : false,
                'moderator' : false
            };
        </script>
    @endif
</head>

<body>

<div class="chat">
    <div class="chat_button" onclick="chat()"></div>
    <div class="chat_loop">
        <div class="chat_top"><em>{{ trans('all.chat.header') }} -</em> SHURZGBETS.COM</div>
        <div class="chat_scroll" id="chat_messages">
        </div>
        <textarea class="chat_mess" placeholder="{{ trans('all.chat.message') }}"></textarea>
        <input class="chat_ok" type="submit" value="ОК" />
    </div>
</div>
@if(!Auth::guest())
<div style="display:none;">
    <div class="modal" id="modal">
        <div class="modal_close arcticmodal-close"></div>
        <div class="modal_top">{!! trans('all.money_modal_header') !!}</div>
        <div class="modal_balance">{{ trans('all.money_modal_balance') }} <em class="balanced">{{ $u->money }}</em><span>{{ trans('all.valute') }}</span></div>
        <div class="modal_balance_buy">
            <input type="text" placeholder="{{ trans('all.money_modal_sum') }}" id="sumadd" />
            <input type="submit" value="{{ trans('all.money_modal_add') }}" class="addbalBtn"/>
        </div>
        <div class="modal_cart">
            <div class="clear"></div>
            <div class="modal_cart_info">
                <p><b>{{ trans('all.card_why') }}</b></p>
                <p>{{ trans('all.card_add_why') }}</p>
                <br />
                <p>{{ trans('all.card_add_change') }}</p>
            </div>
        </div>
    </div>
</div>
@endif
<div class="wrapper">
    <div class="header">
        <a href="/" class="logo"></a>
        @if(Auth::guest())
        <div class="steam_login" style="display:block;"><a href="/login">{!! trans('all.auth') !!}</a></div>
        @else
        <div class="mini_profile">
            <div class="mini_profile_ava"><img src="{{ $u->avatar }}" alt="" /></div>
            <div class="mini_profile_in">
                <div class="mini_profile_name ell">{{ $u->username }}</div>
                <div class="mini_profile_balance">
                    {{ trans('all.money') }} <em class="balanced">{{ $u->money }} {{ trans('all.valute') }}</em>
                    <a href="#" class="plus"></a>
                </div>
            </div>
        </div>
        @endif
        <div class="language">
            <div class="language_text">{{ trans('all.language') }}</div>
            @if(App::getLocale() == 'ru')
            <div class="language_ico"></div>
            <a href="/lang/en"><div class="language_row"></div></a>
            @else
            <div class="language_ico active"></div>
            <a href="/lang/ru"><div class="language_row"></div></a>
            @endif
        </div>
    </div>
    <div class="container">
        <div class="container_l">
            <ul class="nav">
                <li class="ico1"><a href="/">{{ trans('all.menu.main') }}</a></li>
                <li class="ico2"><a href="/top">{{ trans('all.menu.top') }}</a></li>
                <li class="ico3"><a href="/history">{{ trans('all.menu.history') }}</a></li>
                <li class="ico4"><a href="/about">{{ trans('all.menu.about') }}</a></li>
                <li class="ico5"><a href="/giveaway">{{ trans('all.menu.giveaway') }}</a></li>
                <li class="ico6"><a href="/shop">{{ trans('all.menu.shop') }}</a></li>
                @if(!Auth::guest())
                <li class="ico7"><a href="/referals">{{ trans('all.menu.referals') }}</a></li>
                <li class="ico8"><a href="/settings">{{ trans('all.menu.settings') }}</a></li>
                <li class="ico9"><a href="/logout">{{ trans('all.menu.logout') }}</a></li>
                @endif
            </ul>
            <div class="sale">
                <div class="sale_text">{!! trans('all.bonus.text') !!}</div>
            </div>
            @if(!is_null($lastWinner)) 
            <div class="last_win last_winner">
                <div class="last_win_title">{{ trans('all.last_winner.title') }}</div>
                <div class="last_win_ava"><img class="l-w-avatar" src="{{ $lastWinner->winner->avatar }}" alt="" /></div>
                <div class="last_win_name ell l-w-username">{{ $lastWinner->winner->username }}</div>
                <ul>
                    <li>{{ trans('all.last_winner.win') }} <em class="l-w-price">{{ $lastWinner->price }}<span>{{ trans('all.valute') }}</span></em></li>
                    <li>{{ trans('all.last_winner.chance') }} <em class="l-w-chance">{{ \App\Http\Controllers\GameController::_getUserChanceOfGame($lastWinner->winner, $lastWinner) }}%.</em></li>
                </ul>
            </div>
            @else 
            <div class="last_win last_winner" style="display:none;">
                <div class="last_win_title">{{ trans('all.last_winner.title') }}</div>
                <div class="last_win_ava"><img class="l-w-avatar" src="" alt="" /></div>
                <div class="last_win_name ell l-w-username"></div>
                <ul>
                    <li>{{ trans('all.last_winner.win') }} <em class="l-w-price"><span>{{ trans('all.valute') }}</span></em></li>
                    <li>{{ trans('all.last_winner.chance') }} <em class="l-w-chance"></em></li>
                </ul>
            </div>        
            @endif

            @if(!is_null($dayLucky))
            <div class="last_win last_win_day">
                <div class="last_win_title">{{ trans('all.day_lucky.title') }}</div>
                <div class="last_win_ava"><img src="{{ $dayLucky->winner->avatar }}" alt="" /></div>
                <div class="last_win_name ell">{{ $dayLucky->winner->username }}</div>
                <ul>
                    <li>{{ trans('all.day_lucky.win') }} <em>{{ $dayLucky->price }}<span>{{ trans('all.valute') }}</span></em></li>
                    <li>{{ trans('all.day_lucky.chance') }} <em>{{ $dayLucky->chance }}%.</em></li>
                </ul>
            </div>
            @endif
            <a href="https://vk.com/shurzgsupp" target="_blank" class="support">{{ trans('all.support') }}</a>
            <a href="#" class="support">{{ trans('all.online') }} <span class="stats-onlineNow">0</span></a>
            <a href="//www.free-kassa.ru/" style="display: center;" ><img src="//www.free-kassa.ru/img/fk_btn/14.png"></a>
        </div>
        <div class="container_r">
            @yield('content')
        </div>
    </div>
</div>
<div id="news-alert">
    <span class="close-news"></span>
    <h2></h2>
    <p></p>
</div>
<script>(function(a,e,f,g,b,c,d){a.GoogleAnalyticsObject=b;a[b]=a[b]||function(){(a[b].q=a[b].q||[]).push(arguments)};a[b].l=1*new Date;c=e.createElement(f);d=e.getElementsByTagName(f)[0];c.async=1;c.src=g;d.parentNode.insertBefore(c,d)})(window,document,"script","//www.google-analytics.com/analytics.js","ga");ga("create","UA-64317858-6","auto");ga("send","pageview");</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.3.5/socket.io.min.js"></script>
<script>
    @if(!Auth::guest())
    var timeout = false;
    function updateBalance() {
        $.post('{{route('get.balance')}}', function (data) {
            console.log(data);
            $('.balanced').text(data);
        });
    }
    function addTicket(id) {
        if(!timeout) {
            timeout = true;
            $.post('{{route('add.ticket')}}',{id:id}, function(data){
                updateBalance();
                timeout = false;
                return $.notify(data.text, {position: 'bottom middle', className :data.type});
            });
        }
        else {
            return $.notify('Пожалуйста подождите..', {position: 'bottom middle', className :'error'});
        }

    }
    
    @endif

</script>
<script src="{{ asset('assets/js/newapp.js') }}" ></script>
<script src="{{ asset('assets/js/chat.js') }}" ></script>
</body>
</html>
