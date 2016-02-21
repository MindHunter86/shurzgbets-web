$(document).ready(function() {
	var messageList = $('#chat_messages');
	var messageField = $('#sendie');
	var lastMsg = '';
	var lastMsgTime = '';
	var chat = new Firebase("https://csgo-prod.firebaseio.com" + CHAT_CONNECT);
	$('#chatScroll').perfectScrollbar();
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
		if (lastMsgTime && new Date - lastMsgTime < 1000 * 5) {
			$.notify('1 сообщение в 5 секунд');
	        return;
	    }
	    lastMsgTime = new Date;
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
		  }
		});
	}
	function removeMSG() {
		self = this;
		$.ajax({
		  url: '/ajax/chat',
		  type: "POST",
		  data: { 
		  	'type': 'remove',
		  	'id': $(self).attr('data-ids')
		  },
		  success: function(data) {
		  	if(!data.success) {
		  		$.notify(data.text);
		  		return;
		  	} 
		  }
		});
	}
	messageField.keypress(function (e) {
	    if (e.keyCode == 13) {
	    	sendMessage();
	    	return false;
	    }
	});
	chat.on('child_removed', function (snapshot) {
	    var data = snapshot.val();

	    $('.chatMessage[data-uuid='+snapshot.key()+']').remove();
	    $("#chatScroll").perfectScrollbar('update');
	});
	chat.limitToLast(50).on('child_added', function (snapshot) {
	    //GET DATA
	    var data = snapshot.val();
	    data.uuid = snapshot.key();
	    var username = data.username || "Error";
	    var message = data.message;
	    var avatar = data.avatar;
	    var steamid = data.steamid;
	   	if(data.is_moderator) {
	    	username = 'Модератор ('+username+')';
	    }
	    if(data.is_admin) {
	    	username = 'Администратор';
	    	avatar = '/new/images/admin.jpg'
	    }

	    //CREATE ELEMENTS MESSAGE & SANITIZE TEXT
	    var messageElement = $("<div class='chatMessage clearfix' data-uuid='"+data.uuid+"'>");
	    var msg = $('<div class="body"></div>');
	    var nameElement = $("<a href='#' class='login'></a>");
	    var avatarElement = $("<img class='removeMSG' data-ids='"+data.uuid+"' style='height: 32px; width: 32px;' />");
	    avatarElement.attr('src', avatar);
	    nameElement.attr('data-profile', steamid);
	   	if(data.is_moderator) {
	    	nameElement.attr('style', 'color:green;');
	    }
	    if(data.is_admin) {
	    	nameElement.attr('style', 'color:red;');
	    }
	    msg.text(message);
	    nameElement.text(username);
	    messageElement.html(msg).prepend(nameElement).prepend(avatarElement).on('click', '.removeMSG', removeMSG());

	    //ADD MESSAGE
	    messageList.append(messageElement);
	    $('#chatScroll').scrollTop(9999);
	    $('#chatScroll').css('height', $(window).innerHeight() - 120);
	    $("#chatScroll").perfectScrollbar('update');
  	});
});