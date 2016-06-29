var initAjaxToken = function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
};
$(document).ready(function() {
	initAjaxToken();
	$('.sendTrade').click(function() {
        $.ajax({
            url: '/admin/send/ajax',
            type: 'POST',
            dataType: 'json',
            data: {game: $('#sendid').val() },
            success: function (data) {
                if (data.type == 'success') {            
                   	$.notify('Запрошена повторная отправка выигрыша',{className:'success'});
                }
                else {
                    if(data.text) $.notify(data.text);
                }
            },
            error: function () {
                $.notify("Произошла ошибка. Попробуйте еще раз");
            }
        });
    });
    $('.updateReferalCache').click(function() {
        $.ajax({
            url: '/admin/referals/updateCache',
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if (data.type == 'success') {
                    $.notify(data.text, {className:'success'});
                }
                else {
                    if(data.text) $.notify('Ошибка: '+data.text);
                }
            },
            error: function () {
                $.notify("Произошла ошибка. Попробуйте еще раз");
            }
        });
    });
    $('.sendTradeShop').click(function() {
        $.ajax({
            url: '/admin/send/ajaxShop',
            type: 'POST',
            dataType: 'json',
            data: {buy: $('#sendidshop').val() },
            success: function (data) {
                if (data.type == 'success') {            
                    alert('Запрошена повторная отправка товара');
                }
                else {
                    if(data.text) alert(data.text);
                }
            },
            error: function () {
                alert("Произошла ошибка. Попробуйте еще раз");
            }
        });
    });
    $('.sendPrize').click(function() {
        self = this;
        $.ajax({
            url: '/admin/send/ajaxShop',
            type: 'POST',
            dataType: 'json',
            data: {buy: $(self).attr('data-id') },
            success: function (data) {
                if (data.type == 'success') {            
                    alert('Запрошена повторная отправка товара');
                }
                else {
                    if(data.text) alert(data.text);
                }
            },
            error: function () {
                alert("Произошла ошибка. Попробуйте еще раз");
            }
        });   
    });
    $('.addNews').click(function() {
        $.ajax({
            url: '/admin/settings/ajaxNews',
            type: 'POST',
            dataType: 'json',
            data: {
                type: 'add',
                header: $('#news-header').val(),
                message: $('#news-message').val()
            },
            success: function (data) {
                if (data.type == 'success') {
                    $.notify('Оповещеие создано',{className: 'success'});
                }
                else {
                    if(data.text) $.notify(data.text);
                }
            },
            error: function () {
                $.notify("Произошла ошибка. Попробуйте еще раз");
            }
        });
    });
    $('.removeNews').click(function() {
        $.ajax({
            url: '/admin/settings/ajaxNews',
            type: 'POST',
            dataType: 'json',
            data: {
                type: 'remove'
            },
            success: function (data) {
                if (data.type == 'success') {
                    $.notify('Оповещеие удалено',{className: 'success'});
                    $('#news-header').val('');
                    $('#news-message').val('');
                }
                else {
                    if(data.text) $.notify(data.text);
                }
            },
            error: function () {
                $.notify("Произошла ошибка. Попробуйте еще раз");
            }
        });
    });
    $('.stakesOn').click(function() {
        $.ajax({
            url: '/admin/settings/ajaxStakes',
            type: 'POST',
            dataType: 'json',
            data: {
                type: 'on'
            },
            success: function (data) {
                if (data.type == 'success') {
                    $.notify('Прием ставок возобновлен',{className: 'success'});
                    $('.stakesOff').show();
                    $('.stakesOn').hide();
                    $('#stake-info').hide();
                }
                else {
                    if(data.text) $.notify(data.text);
                }
            },
            error: function () {
                $.notify("Произошла ошибка. Попробуйте еще раз");
            }
        });
    });
    $('.stakesOff').click(function() {
        $.ajax({
            url: '/admin/settings/ajaxStakes',
            type: 'POST',
            dataType: 'json',
            data: {
                type: 'off'
            },
            success: function (data) {
                if (data.type == 'success') {
                    $.notify('Прием ставок отключен!',{className: 'success'});
                    $('.stakesOff').hide();
                    $('.stakesOn').show();
                    $('#stake-info').show();
                }
                else {
                    if(data.text) $.notify(data.text);
                }
            },
            error: function () {
                $.notify("Произошла ошибка. Попробуйте еще раз");
            }
        });
    });
    $('#profile-btn').click(function() {
        $('#profile-modal').modal();
    });
    
    $("#example1").DataTable( {
        "lengthMenu": [ 100, 25, 50, 75, 1000 ],
        "order": [[ 4, "desc" ]]
    } );
    $("#example2").DataTable( {
        "order": [[ 1, "desc" ]]
    } );
});