<!doctype html>
<html class="no-js" lang="ru">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>ITEMUP - Магазин Counter Strike: Global Offensive</title>
    <meta name="keys" content="csgomarket, csgo market, магазин csgo" />
    <meta name="description" content="Магазин скинов CS:GO в котором можно покупать и продавать предметы" />
    <meta name="viewport" content="width=1200">
    <meta name="csrf-token" content="{!!  csrf_token()   !!}">

    <link rel="stylesheet" href="{{ asset('assets/css/normalize.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/shop/css/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/shop/css/style.css') }}" />
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700&subset=latin,cyrillic' rel='stylesheet' type='text/css' />
    <script src="{{ asset('assets/shop/js/app.js') }}"></script>
    <script src="{{ asset('assets/shop/js/main1.js') }}"></script>
    <script src="{{ asset('assets/shop/js/main2.js') }}"></script>
</head>
<body>
<div class="main-container page-history">
    <section class="main">
        <div class="head-content">
            <a class="logotype" href="/"></a>
            <nav class="header-nav">
                <ul id="navbar" class="navbar-nav">
                    <li  class="active" >
                        <a href="/shop">Купить</a>
                    </li>
                </ul>

                <div class="navbar-middle">
                    <li><a href="/comments">Отзывы</a></li>
                    <li><a href="#" data-modal="#botsModal">Инвентарь бота</a></li>
                    <li><a href="#" data-modal="#contactsModal">Контакты</a></li>
                </div>

                <ul class="navbar-nav navbar-right">
                    <ul class="rightnavskos">
                        <li class="balance-wrap">
                            <div class="balance">
                                На счету: <span>{{ $u->money }}</span> руб
                            </div>
                            <div class="add-funds-btn" data-block="#balanceInput"></div>
                            <div id="balanceInput" class="add-balance-input" style="display:none;">
                                <div class="form-control">
                                    <form action="https://unitpay.ru/pay/{{ Config::get('unitpay.publickey') }}">
                                        <input type="hidden" name="account" value="{{ $u->id }}">
                                        <input id="sum-input" class="numeric" name="summ" placeholder="Введите сумму" type="text">
                                        <input type="hidden" name="desc" value="Описание платежа">
                                        <input class="btn" class="add-funds-button" type="submit" value="Пополнить">
                                    </form>
                                </div>
                            </div>
                        </li>
                        <li class="user-profile-container">
                            <div class="user-profile" data-block="#profileContainer">
                                <img src="{{ $u->avatar }}">
                                <span>настройки</span>
                            </div>

                        </li>
                    </ul>

                    <div id="profileContainer" class="user-profile-box alwaysShow">
                        <div class="user-profile-container">

                            <div class="user-profile-box-left">
                                <img src="{{ $u->avatar }}">
                                <div class="user-profile-box-info">
                                    <div class="user-profile-info-head">
                                        Вы вошли как: {{ $u->username }}
                                        <div class="user-logout-btn">
                                            <a href="/logout">Выйти</a>
                                        </div>
                                    </div>
                                    <div class="user-profile-info-body">
                                        <a href="/shop/history" class="user-profile-btn ">История покупок</a>
                                    </div>
                                </div>
                            </div>

                            <div class="user-profile-box-right">
                                <div class="offer-link-head">
                                    Укажите вашу ссылку на обмен в Steam
                                    <a class="helper-link" href="http://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url" target="_blank">Как узнать ссылку</a>
                                </div>
                                <div class="offer-link-body">
                                    <input id="offer-link" style="width: 100%;" value="{{ $u->trade_link }}" placeholder="Вставьте сюда вашу ссылку на обмен" type="url">
                                </div>
                            </div>
                        </div>
                    </div>
                </ul>

            </nav>
        </div>
        <div class="body-content">
            <h2 class="history-page-title" style="margin-bottom: 15px; margin-top: -5px;">История ваших покупок на сайте:</h2>

            <div class="memoMsg">
                Если после покупки у вас стоит статусе будет стоять "Ошибка" не переживайте - деньги будут возвращены на баланс. Возврат средств происходит автоматически каждый час.<br>
                Если у вас была введена не рабочая ссылка на обмен, исправьте ссылку на рабочую, дождитесь возврата средств и повторите покупку.<br>
                Если в статусе написано, что у вас бан трейда, тогда не пробуйте покупать снова, а подождите пока у вас закончится ограничение на обмен и только тогда продолжайте покупки.<br>
                А также, если вдруг у вас стоит статус "Отправлено", но предложение обмена вам не пришло (или вы его случайно отклонили) деньги тоже будут автоматически возвращены на баланс в течении часа.
            </div>

            <div class="purchase-history-table">
                <table>
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Предмет</th>
                        <th>Качество</th>
                        <th>Цена</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td><div class="player-jackpot">{{ $item->buy_at }}</div></td>
                            <td>
                                <div class="player-avatar inline-block">
                                    <a href="#"><img src="https://steamcommunity-a.akamaihd.net/economy/image/class/{{ \App\Http\Controllers\GameController::APPID }}/{{ $item->classId }}/50fx50f" alt=""></a>
                                </div>
                                <div class="player-name inline-block">
                                    {{ $item->name }}
                                </div>
                            </td>
                            <td><div>{{ $item->quality }} <span></span></div></td>
                            <td><div>{{ $item->price }} <span>руб.</span></div></td>

                            @if($item->status == \App\Shop::ITEM_STATUS_SOLD)
                                <td><div class="game-status processing">Отправка предмета</div></td>
                            @elseif($item->status == \App\Shop::ITEM_STATUS_SEND)
                                <td><div class="game-status">Предмет отправлен</div></td>
                            @elseif($item->status == \App\Shop::ITEM_STATUS_NOT_FOUND)
                                <td><div class="game-status error">Предмет не найден</div></td>
                            @elseif($item->status == \App\Shop::ITEM_STATUS_ERROR_TO_SEND)
                                <td><div class="game-status error">Ошибка отправки</div></td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">Вы не делали покупок</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                <div class="pagination-history">

                    <div>

                    </div>
                </div>
            </div></div>
    </section>
</div>

<div style="display:none;">
    <div id="buyModal" class="modal-window">
        <div class="detailed-info">
            <div class="modal-close arcticmodal-close"></div>
            <div class="detailed-info-body">
                <div class="detailed-info-body-left">
                    <div class="detailed-image">
                        <img src="" />
                    </div>
                    <a href="#" class="explore-game" style="display: none; position: relative; z-index: 3;">осмотреть в игре</a>
                </div>
                <div class="detailed-info-body-right">
                    <h2 class="name"></h2>
                    <div class="detailed-info-desc-wrap">
                        <div class="detailed-info-desc">
                            <dl>
                                <dt>Редкость</dt>
                                <dd class="rarity"></dd>
                            </dl>
                        </div>
                        <div class="detailed-info-desc">
                            <dl>
                                <dt>качество</dt>
                                <dd class="type2"></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="detailed-info-price-wrap">
                        <div class="detailed-info-price">
                            <dl>
                                <dt>
                                <div class="detailed-steam-price steamPrice">0 <span>руб</span></div>
                                </dt>
                                <dd>цена в steam</dd>
                            </dl>
                        </div>
                        <div class="detailed-info-price">
                            <dl>
                                <dt class="detailed-our-price ourPrice">0 <span>руб</span></dt>
                                <dd>наша цена</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="detailed-info-footer">
                <div class="detailed-info-footer-left">
                    <div class="detailed-info-checkbox">
                        <input id="agreement" type="checkbox" checked="checked">
                        <label for="agreement">
                            Я согласен с <a href="#">условиями</a> и подтверждаю, <br>что не имею ограничений на обмен в Steam.
                        </label>
                    </div>
                    <div class="detailed-info-time">
                        Вы должны будете принять обмен в течении <span>1 часа</span>
                    </div>
                </div>
                <div class="detailed-info-footer-right">
                    <div class="buy-btn">Купить</div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div id="buySuccessMsg" class="message-box message-success" style="display:none;">
    <h2>Вы успешно приобрели предмет</h2>
    <div class="message-text">
        <p>Трейд от нашего бота будет отправлен вам в течении <span>3-х</span> минут.</p>
        <p>Если в течении этих <span>3-х</span> минут вы совершите еще покупки, то все предметы будут отправлены вам в одном трейде от нашего бота.</p>
    </div>
    <div class="ok-btn-block">
        <div class="ok-btn message-box-close">ок</div>
    </div>
</div>

<div style="display:none;">
    <div id="contactsModal">
        <div class="supModalbsc">
            <div class="modal-close arcticmodal-close"></div>
            <h2 class="history-page-title">Служба поддержки</h2>

            <div style="margin-bottom: 7px; border-bottom: 1px dashed #6A717B; padding-bottom: 7px;">
                <span style="color: #ECA594;">Вопрос:</span> Мне не пришел предмет!<br>
                <span style="color: #B7E5B7;">Ответ:</span> Отправка предметов может занимать до 30 минут (в зависимости от загруженности ботов), а также обратите внимание на то, что в настройках приватности вашего аккаунта Steam ваш инвентарь должен быть открыт: <a href="http://steamcommunity.com/id/me/edit/settings/" style="color: #86C9EA;" target="_blank">http://steamcommunity.com/id/me/edit/settings/</a>
            </div>

            <div style="margin-bottom: 7px; border-bottom: 1px dashed #6A717B; padding-bottom: 7px;">
                <span style="color: #ECA594;">Вопрос:</span> Пополнил баланс, а средства не зачислились на аккаунт!<br>
                <span style="color: #B7E5B7;">Ответ:</span> Пополнения через мобильные платежи и банковские карточки могут обрабатываться до 15 минут.
            </div>

            <div class="vksupIf">Если вы здесь не нашли ответа на ваш вопрос, тогда вы можете задать его нашему саппорту<br>через эту форму отправки сообщений в VK.</div>
            <a class="vksupBtn" href="http://vk.com/write303350375" target="_blank">Отправить сообщение саппорту в ВК</a>
        </div>
    </div>
</div>



<div style="display:none;">
    <div id="botsModal">
        <div class="supModalbsc">
            <div class="modal-close arcticmodal-close"></div>
            <h2 class="history-page-title">Инвентарь ботов</h2>

            <div style="">
                Вы можете сами убедиться в том, что все продаваемые предметы на нашем сайте есть в наличии у наших ботов:
            </div>

            <div class="botList clearfix">
                <img src="https://cdn.akamai.steamstatic.com/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_medium.jpg"></img>
                <div style="float: left; margin-top: 2px;">
                    <span>CSGOMARKET.CC #1</span><br>
                    <a href="http://steamcommunity.com/id/csgomarket1/inventory/" target="_blank">Посмотреть инвентарь</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>