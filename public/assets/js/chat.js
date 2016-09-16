$(document).ready(function() {
	var messageList = $('#chat_messages');
	var messageListAdd = $('.chat_scroll .mCSB_container');
    var moderatorElement = $("<div class='chat_moderator_controls'>" +
    "<div class='chat_delete_msg'></div>" +
    "<div class='chat_ban_user'></div>" +
    "<div class='ban_time_group'><input class='chat_ban_time' type='number' value='60' min='1' /><label>минут</label> или <a href='#'>навсегда</a></div>" +
    "</div>");
	var messageField = $('.chat_mess');
	var lastMsg = '';
	var lastMsgTime = '';
    var chatNode = io.connect(SITE_URL, {path:'/chatnode', secure: true, 'force new connection': true });
    chatNode
        .on('chat_history', function(msg) {
            msg.forEach(function(ms) {
               addMessageToChat(ms);
            });
        })
        .on('chat_new_message', function(msg) {
            addMessageToChat(msg);
        })
        .on('chat_remove_message', function (msg) {
            $('.chat_info[data-msgid='+msg.id+']').remove();
        })
        .on('chat_ban', function(data) {
            info = data;
            if (info.steamid == USER_ID)
                $.notify('Вы были забанены в чате до '+info.bantime, {className:"error"});
        });

	messageList.mCustomScrollbar();

    function addMessageToChat(data) {
        var username = data.username || "Error";
        var message = data.text;
        var avatar = data.avatar;
        var steamid = data.steamid;

        var avatarElement = $("<div class='chat_ava'><img /></div>");
        var nameElement = $("<div class='chat_name'></div>");
        var msgElement = $("<div class='chat_text'></div>");
        var bodyElement = $("<div class='chat_info' data-steamid='"+steamid+"' data-msgid='"+data.id+"'></div>");
        var msgBodyElement = $("<div class='chat_in'></div>");

        avatarElement.find("img").attr('src', avatar);
        nameElement.text(username);
        msgElement.text(message);
        if (role.admin || role.moderator) {
            msgElement.after(moderatorElement.clone());
        }
        msgBodyElement.prepend(msgElement).prepend(nameElement);

        if(data.is_vip == "1") {
            nameElement.attr('style', 'color:orange;');
        }
        if(data.is_moderator == "1") {
            nameElement.attr('style', 'color:green;');
        }
        bodyElement.prepend(msgBodyElement).prepend(avatarElement);
        $('.chat_scroll .mCSB_container').append(bodyElement);
        messageList.mCustomScrollbar('update');
        setTimeout(function() {
            messageList.mCustomScrollbar('scrollTo', 'bottom');
        }, 500);
    }

	function sendMessage() {
      	var message = messageField.val();
	    var maxlength = 200;
	    if (message.length > maxlength) {
	    	$.notify('Максимум 200 символов');
	        return;
	    }
		message = message.trim();
	    if (!message) {
	    	$.notify('Вы ничего не ввели!');
	        return;
	    }
		if (lastMsgTime && new Date - lastMsgTime < 1000) {
			$.notify('1 сообщение в секунду');
	        return;
	    }
	    lastMsg = message;
      	$.ajax({
		  url: '/ajax/chat',
		  type: "POST",
		  data: { 
		  	'type': 'push',
		  	'message': message
		  },
		  success: function(data) {
		  	if(!data.success) {
		  		$.notify(data.text);
		  		return;
		  	} 
		  	messageField.val('');
            lastMsgTime = new Date;
		  }
		});
	}
	$('#chat_messages').on('click', '.chat_delete_msg',function() {
       	self = this;
		$.ajax({
		  url: '/ajax/chat',
		  type: "POST",
		  data: { 
		  	'type': 'remove',
		  	'id': $(self).parents('.chat_info').attr('data-msgid')
		  },
		  success: function(data) {
		  	if(!data.success) {
		  		$.notify(data.text, {className:"error"});
		  		return;
		  	}
            $.notify(data.text);
		  }
		});
        return false;
    });
    function banUser(steamid, minutes) {
        $.ajax({
            url: '/ajax/chat',
            type: "POST",
            data: {
                'type': 'ban',
                'steamid': steamid,
                'time' : minutes
            },
            success: function(data) {
                if(!data.success) {
                    $.notify(data.text, {className:"error"});
                    return false;
                }
                $.notify(data.text);
                return true;
            }
        });
        return true;
    }
    $('#chat_messages').on('click', '.chat_ban_user',function() {
        self = this;
        $(self).siblings('.ban_time_group').toggle();
        return false;
    });
    $('#chat_messages').on('keypress', '.chat_ban_time',function(e) {
        if (e.keyCode == 13) {
            self = this;
            var steamid = ($(self).parents('.chat_info').attr('data-steamid'));
            if (banUser(steamid, $(self).val())) {
                $(self).parent().hide();
            }
            return false;
        }
    });
    $('#chat_messages').on('click', '.ban_time_group a',function() {
        var steamid = ($(this).parents('.chat_info').attr('data-steamid'));
        if (banUser(steamid,-1)) {
            $(this).parent().hide();
        }
        return false;
    });
	messageField.keypress(function (e) {
	    if (e.keyCode == 13) {
	    	sendMessage();
	    	return false;
	    }
	});
	$('.chat_ok').click(function() {
		sendMessage();
		return false;
	});
});
