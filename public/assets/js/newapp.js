var BANNED_DOMAINS = '(csgofast|csgolucky|csgocasino|game-luck|g2a|csgostar|hellstore|cs-drop|csgo|csgoshuffle|csgotop|csbets|csgobest|csgolike|fast-jackpot|skins-up|hardluck-shop|csgogamble|csgohot|csgofairplay|csgoluxe|csgo1|csgo-chance|csgofb|ezyskins|ezpzskins|csgokill|csgoway|csgolotter|csgomany|csrandom|csgo-winner|csgoninja|csgopick|csgodraw|csgoeasy|csgojackpot|game-raffle|csgonice|kinguin|realskins|csgofart|csgetto|csgo-rand|csgo-jackpot|timeluck|forgames|csgobig|csgo-lottery|csgovictory|csgotrophy|csgo-farming|ezskinz)\.(ru|com|net|gl|one|c|pro)';
$(document).ready(function() {

    ITEMUP.init();
    var helpers = {
        showPopup: function(t, e, o) {
            var n = this,
                a = this.e(t, o && o.width || 950, o && o.height || 670),
                i = setInterval(function() {
                    try {
                        this.e = a.closed || void 0 === a.closed
                    } catch (t) {
                        return
                    }
                    this.e && (clearInterval(i), n.o())
                }, 100)
        }, e: function(t, e, o) {
            var n = "undefined" != typeof window.screenX ? window.screenX : window.screenLeft,
                a = "undefined" != typeof window.screenY ? window.screenY : window.screenTop,
                i = "undefined" != typeof window.outerWidth ? window.outerWidth : document.body.clientWidth,
                s = "undefined" != typeof window.outerHeight ? window.outerHeight : document.body.clientHeight - 22,
                r = n + (i - e) / 2,
                l = a + (s - o) / 2,
                c = "width=" + e + ",height=" + o + ",left=" + r + ",top=" + l + ",scrollbars=yes",
                d = window.open(t, "SteamCommunity", c);
            if ("undefined" == typeof d) {
                var p = new Error("The deposit popup was blocked by the browser");
                throw p.attemptedUrl = t, p
            }
            return d.focus && d.focus(), d
        }
    }
    $('.depositModal').click(function(e) {
        return helpers.showPopup("https://shurzgbets.com/deposit"), !1
    });

    $('.hoax-button').click(function() {
        var that = $(this).prev();
        $.ajax({
            url: '/giveaway/accept',
            type: 'POST',
            success: function(data) {
                if(!data.success) {
                    $('.hoax-button').notify(data.msg, {position: 'left center', className :"error"});
                }
            }
        })
    });
    $('.addbalBtn').click(function() {
        $.ajax({
            url: '/merchant',
            type: 'POST',
            dataType: 'json',
            data: {sum: $('#sumadd').val() },
            success: function (data) {
                if (data.status == 'success') {
                    document.location.href = data.url;
                }
                else {
                    if(data.msg) $(btn).notify(data.text, {position: 'bottom middle', className :"error"});
                }
            },
            error: function () {
                $(btn).notify("Произошла ошибка. Попробуйте еще раз", {position: 'bottom middle', className :"error"});
            }
        });
    });
    $('.create-promo').click(function() {
        var that = $(this).prev();
        $.ajax({
            url: '/promo/create',
            type: 'POST',
            dataType: 'json',
            data: {code: $('.promo-create-text').val() },
            success: function (data) {
                if (data.success) {
                    that.notify(data.text, {autoHideDelay: 1000,position: 'bottom middle', className :"success"});
                }
                else {
                    if(data.text) that.notify(data.text, {position: 'bottom middle', className :"error"});
                }
            },
            error: function () {
                that.notify("Произошла ошибка. Попробуйте еще раз", {position: 'bottom middle', className :"error"});
            }
        });
        return false;
    });
    $('.accept-promo').click(function() {
        var that = $(this).prev();
        $.ajax({
            url: '/promo/accept',
            type: 'POST',
            dataType: 'json',
            data: {code: $('.promo-accept-text').val()},
            success: function (data) {
                if (data.success) {
                    that.notify(data.text, {autoHideDelay: 1000,position: 'top middle', className :"success"});
                }
                else {
                    if(data.text) that.notify(data.text, {position: 'top middle', className :"error"});
                }
            },
            error: function () {
                that.notify("Произошла ошибка. Попробуйте еще раз", {position: 'top middle', className :"error"});
            }
        });
        return false;
    });
    $('.save-link, .save-link2').click(function () {
        var that = $(this).prev();
        $.ajax({
            url: '/settings/save',
            type: 'POST',
            dataType: 'json',
            data: {trade_link: $('.trade_link').val()},
            success: function (data) {
                if (data.status == 'success') {
                    that.notify(data.msg, {autoHideDelay: 1000,position: 'left middle', className :"success"});
                    setTimeout( function() {
                        $('.notradehide').hide();
                    }, 1500);
                }
                else {
                    if(data.msg) that.notify(data.msg, {position: 'left middle', className :"error"});
                }
            },
            error: function () {
                that.notify("Произошла ошибка. Попробуйте еще раз", {position: 'left middle', className :"error"});
            }
        });
        return false;
    });
});

function getRarity(type) {
    var rarity = '';
    var arr = type.split(',');
    if (arr.length == 2) type = arr[1].trim();
    if (arr.length == 3) type = arr[2].trim();
    if (arr.length && arr[0] == 'Нож') type = '★';
    switch (type) {
        case 'Армейское качество':      rarity = 'milspec'; break;
        case 'Запрещенное':             rarity = 'restricted'; break;
        case 'Засекреченное':           rarity = 'classified'; break;
        case 'Тайное':                  rarity = 'covert'; break;
        case 'Ширпотреб':               rarity = 'common'; break;
        case 'Промышленное качество':   rarity = 'common'; break;
        case '★':                       rarity = 'rare'; break;
        case 'card':                    rarity = 'card'; break;
    }
    return rarity;
}

function n2w(n, w) {
    n %= 100;
    if (n > 19) n %= 10;

    switch (n) {
        case 1: return w[0];
        case 2:case 3:case 4: return w[1];
        default: return w[2];
    }
}
function lpad(str, length) {
    while (str.toString().length < length)
        str = '0' + str;
    return str;
}

function replaceLogin(login) {
    var reg = new RegExp(BANNED_DOMAINS, 'i');
    return login.replace(reg, "itemup.ru");
}

if (START) {
    var socket = io.connect('https://joyskins.top', { secure: true });

    socket
        .on('newDeposit', function(data) {
            data = JSON.parse(data);
            $('.game_bank').text(Math.round(data.gamePrice));
            $('.game_bar').css('width', data.itemsCount + '%');
            html_chances = '';
            data.chances = sortByChance(data.chances);
            data.chances.forEach(function(info){
                html_chances += '<li><b>'+info.chance+'%</b><img src="'+info.avatar+'" alt="" /></li>';
            });
            $('.chance').show();
            $('.chances').html(html_chances);
        })
        .on('timer', function (time) {
            if(timerStatus) {
                console.log(time);
                timerStatus = false;

                $('.gameEndTimer').empty().countdown({seconds: time});
            }
        })
        .on('slider', function (data) {
            if(ngtimerStatus) {
                ngtimerStatus = false;
                var users = data.users;
                users = mulAndShuffle(users, Math.ceil(110 / users.length));
                users[6] = data.winner;
                users[100] = data.winner;
                html = '';
                users.forEach(function (i) {
                    html += '<li><img src="' + i.avatar + '"></li>';
                });

                $('.ngtimer').empty().countdown({seconds: data.time});

                $('.game_stats').hide();
                $('.game_winner').show();

                $('.all-players-list').html(html);
                $('.game_bank').text(data.game.price);
                $('.win_ticket').text('---');
                $('.win_username').text('---');
                $('.all-players-list').removeClass('active0 active1 active2 active3 active4 active5 active6 active7');

                var randoms = randomInteger(0,7);
                if(data.showSlider) {
                    setTimeout(function () {
                        console.log(randoms);
                        $('.all-players-list').addClass('active'+randoms);
                    }, 500);
                }
                var timeout = data.showSlider ? 10 : 0;
                setTimeout(function () {
                    $('.round_number').text(data.round_number);
                    $('.game_round_number').show();

                    $('.win_ticket').text('#'+data.ticket);
                    $('.win_username').text(data.winner.username + ' ('+data.chance +'%)');
                }, 1100 * timeout);
            }
        })
        .on('newGame', function (data) {
            $('.game_round_number').hide();
            $('.game_stats').show();

            $('.game_winner').hide();

            $('.stats-gamesToday').text(data.today);
            $('.stats-uniqueUsers').text(data.userstoday);
            $('.stats-wintoday').text(data.maxwin);
            $('.game_num span').text(data.id);
            $('.game_bank').text(0);
            $('.game_hash_number').text(data.hash);
            $('.game_bar').css('width','0%');
            timerStatus = true;
            ngtimerStatus = true;
        })
    var declineTimeout,
        timerStatus = true,
        ngtimerStatus = true,
        lotteryTimerStatus = true;
}

function mergeWithDescriptions(items, descriptions) {
    return Object.keys(items).map(function(id) {
        var item = items[id];
        var description = descriptions[item.classid + '_' + (item.instanceid || '0')];
        for (var key in description) {
            item[key] = description[key];

            delete item['icon_url'];
            delete item['icon_drag_url'];
            delete item['icon_url_large'];
        }
        return item;
    })
}

function mulAndShuffle(arr, k) {
    var
        res = [],
        len = arr.length,
        total = k * len,
        rand, prev;
    while (total) {
        rand = arr[Math.floor(Math.random() * len)];
        if (len == 1) {
            res.push(prev = rand);
            total--;
        }
        else if (rand !== prev) {
            res.push(prev = rand);
            total--;
        }
    }
    return res;
}

$(document).on('click', '.depositCardBtn, ._carts', function () {
    $.post('https://itemup.ru/getBalance', function (data) {
        console.log(data);
        $('#balanced').text(data);
    });

    $('#upCards').arcticmodal();

    //updateCards();

    return false;
});
function setCookie(name, value, options) {
  options = options || {};

  var expires = options.expires;

  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires * 1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) {
    options.expires = expires.toUTCString();
  }

  value = encodeURIComponent(value);

  var updatedCookie = name + "=" + value;

  for (var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];
    if (propValue !== true) {
      updatedCookie += "=" + propValue;
    }
  }

  document.cookie = updatedCookie;
}
/*function updateChat() { 
    $('.info-md5').css('margin', '0 180px');
    $('.stats').css('margin', '16px auto 18px 70px');
    $('.information').css('margin', '0 auto 25px 70px');
    $('.gf').css('margin', '0 70px');
    $('.hf').css('margin', '0 auto');
    $('.bc').css('margin', '10px auto 0 70px');
}*/
function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}
function sortByChance(arrayPtr){
    var temp = [],
        item = 0;
    for (var counter = 0; counter < arrayPtr.length; counter++)
    {
        temp = arrayPtr[counter];
        item = counter-1;
        while(item >= 0 && arrayPtr[item].chance < temp.chance)
        {
            arrayPtr[item + 1] = arrayPtr[item];
            arrayPtr[item] = temp;
            item--;
        }
    }
    return arrayPtr;
}

function randomInteger(min, max) {
  var rand = min + Math.random() * (max - min)
  rand = Math.round(rand);
  return rand;
}