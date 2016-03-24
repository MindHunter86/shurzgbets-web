<!DOCTYPE html>
<html>
<head>
    <title>SHURZGBETS.COM</title>
    <meta charset="utf-8">
    <meta property="og:title" content="JOYSKINS.TOP" />
    <meta name="keywords" content="Рулетка cs go для бомжей с минимальной ставкой 1 рубль. Именно рулетки кс го с минимальной ставкой 1 рубль самые доступные для бомжей. JOYSKINS.TOP - это cs go рулетка не больше 100 рублей без минимальной ставки. " />
    <meta name="description" content="Рулетка cs go для бомжей с минимальной ставкой 1 рубль. Именно рулетки кс го с минимальной ставкой 1 рубль самые доступные для бомжей. JOYSKINS.TOP - это cs go рулетка не больше 100 рублей без минимальной ставки. " />
    <meta name="csrf-token" content="{!!  csrf_token()   !!}">
    <link type="text/css" rel="stylesheet" href="{{ asset('shurzg/css/style.css') }}" />
    <link type="text/css" rel="stylesheet" href="{{ asset('shurzg/css/jquery.mCustomScrollbar.css') }}" />
    <link rel="shortcut icon" href="{{ asset('shurzg/images/favicon.ico') }}" />
    <script type="text/javascript" src="{{ asset('shurzg/js/jquery-1.9.1.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/jquery.cookie.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/jquery.smoothscroll.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('shurzg/js/script.js') }}"></script>

    <script src="{{ asset('assets/js/inc.js') }}" ></script>
    <script src="{{ asset('assets/js/main.js') }}" ></script>

    <script>
        var CHAT_CONNECT = '/chat/4';
    </script>

    @if(!Auth::guest())
        <script>
            const USER_ID = '{{ $u->steamid64 }}';
            var START = true;
        </script>
    @else
        <script>
            const USER_ID = '0';
            var START = true;
        </script>
    @endif
</head>

<body>

<div class="chat">
    <div class="chat_button" onclick="chat()"></div>
    <div class="chat_loop">
        <div class="chat_top"><em>Чат -</em> SHURZGBETS.COM</div>
        <div class="chat_scroll mCustomScrollbar" id="chat_messages">
        </div>
        <textarea class="chat_mess" placeholder="Введите сообщение..."></textarea>
        <input class="chat_ok" type="submit" value="ОК" />
    </div>
</div>
<div class="wrapper">
    <div class="header">
        <a href="#" class="logo"></a>
        @if(Auth::guest())
        <div class="steam_login" style="display:block;"><a href="/login">Войти через <b>STEAM</b></a></div>
        @else
        <div class="mini_profile">
            <div class="mini_profile_ava"><img src="{{ $u->avatar }}" alt="" /></div>
            <div class="mini_profile_in">
                <div class="mini_profile_name ell">{{ $u->username }}</div>
                <div class="mini_profile_balance">
                    Баланс: <em>{{ $u->money }} руб.</em>
                    <a href="#" class="plus"></a>
                </div>
            </div>
        </div>
        @endif
        <div class="language">
            <div class="language_text">Язык:</div>
            <div class="language_ico"></div>
            <div class="language_row" onclick="language()"></div>
        </div>
    </div>
    <div class="container">
        <div class="container_l">
            <ul class="nav">
                <li class="ico1"><a href="/">Начать играть</a></li>
                <li class="ico2"><a href="/top">Топ игроков</a></li>
                <li class="ico3"><a href="/history">История игр</a></li>
                <li class="ico4"><a href="/about">О сайте</a></li>
                <li class="ico5"><a href="/">Розыгрыш</a></li>
                <li class="ico6"><a href="/shop">Магазин</a></li>
                @if(!Auth::guest())
                <li class="ico7"><a href="/referals">Реф.система</a></li>
                <li class="ico8"><a href="/settings">Настройки</a></li>
                <li class="ico9"><a href="/logout">Выход</a></li>
                @endif
            </ul>
            <div class="sale">
                <div class="sale_text">Добавь <b>SHURZG</b><em>BETS</em> к своему Steam никнейму и получи <b>5%</b> бонус к выигрышу!</div>
            </div>
            @if(!is_null($lastWinner)) 
            <div class="last_win">
                <div class="last_win_title">Последний победитель:</div>
                <div class="last_win_ava"><img src="images/ava.jpg" alt="" /></div>
                <div class="last_win_name ell">$lastWinner->winner->username</div>
                <ul>
                    <li>Выигрыш: <em>$lastWinner->price<span>руб.</span></em></li>
                    <li>Шанс на победу: <em>{{ \App\Http\Controllers\GameController::_getUserChanceOfGame($lastWinner->winner, $lastWinner) }}%.</em></li>
                </ul>
            </div>
            @endif
            <div class="last_win last_win_day">
                <div class="last_win_title">Счастливчик дня:</div>
                <div class="last_win_ava"><img src="images/ava.jpg" alt="" /></div>
                <div class="last_win_name ell">Alexander Boom™</div>
                <ul>
                    <li>Выигрыш: <em>48 800<span>руб.</span></em></li>
                    <li>Шанс на победу: <em>48.4%.</em></li>
                </ul>
            </div>
            <a href="#" class="support">Техническая поддержка</a>
        </div>
        @yield('content')
    </div>
</div>
<script>(function(a,e,f,g,b,c,d){a.GoogleAnalyticsObject=b;a[b]=a[b]||function(){(a[b].q=a[b].q||[]).push(arguments)};a[b].l=1*new Date;c=e.createElement(f);d=e.getElementsByTagName(f)[0];c.async=1;c.src=g;d.parentNode.insertBefore(c,d)})(window,document,"script","//www.google-analytics.com/analytics.js","ga");ga("create","UA-64317858-6","auto");ga("send","pageview");</script>
<script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>
<script>
    @if(!Auth::guest())
    var timeout = false;
    function updateBalance() {
        $.post('{{route('get.balance')}}', function (data) {
            console.log(data);
            $('#balanced').text(data);
        });
    }
    function addTicket(id, btn) {
        if(!timeout) {
            timeout = true;
            $.post('{{route('add.ticket')}}',{id:id}, function(data){
                updateBalance();
                timeout = false;
                return $(btn).notify(data.text, {position: 'bottom middle', className :data.type});
            });
        }
        else {
            return $(btn).notify('Пожалуйста подождите..', {position: 'bottom middle', className :'error'});
        }

    }
    
    @endif

</script>
<script type="text/javascript" src="{{ asset('new/js/lemmon-slider.js') }}"></script>
<script src="{{ asset('assets/js/firebase.js') }}" ></script>
<script src="{{ asset('assets/js/newapp.js') }}" ></script>
<script src="{{ asset('assets/js/chat.js') }}" ></script>
</body>
</html>
