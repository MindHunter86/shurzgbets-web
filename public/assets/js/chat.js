$(document).ready(function() {
	var messageList = $('#chat_messages');
	var messageField = $('#sendie');
	var lastMsg = '';
	var lastMsgTime = '';
	var chat = new Firebase("https://csgo-prod.firebaseio.com" + CHAT_CONNECT);
	//messageList.mCustomScrollbar();
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
	$('#chatScroll').on('click', '.removeMSG',function() {
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
        return false;
    });
	messageField.keypress(function (e) {
	    if (e.keyCode == 13) {
	    	sendMessage();
	    	return false;
	    }
	});
	var msgs = chat.limitToLast(50);
	msgs.on('child_removed', function (snapshot) {
	    var data = snapshot.val();

	    $('.chatMessage[data-uuid='+snapshot.key()+']').remove();
	    //messageList.mCustomScrollbar();
	});
	msgs.on('child_added', function (snapshot) {
	    var data = snapshot.val();
	    data.uuid = snapshot.key();
	    var username = data.username || "Error";
	    var message = data.message;
	    var avatar = data.avatar;
	    var steamid = data.steamid;

	    var avatarElement = $("<div class='chat_ava'><img class='removeMSG' data-ids='"+data.uuid+"' /></div>");
	    var nameElement = $("<div class='chat_name'></div>");
	    var msgElement = $("<div class='chat_text'></div>");
	    var bodyElement = $("<div class='chat_info' data-uuid='"+data.uuid+"'></div>");
	    var msgBodyElement = $("<div class='chat_in'></div>");

	    avatarElement.find("img").attr('src', avatar);
	    nameElement.text(username);
	    msgElement.text(message);
	    msgBodyElement.prepend(msgElement).prepend(nameElement);

	   	if(data.is_vip == "1") {
	    	nameElement.attr('style', 'color:orange;');
	    }
	   	if(data.is_moderator == "1") {
	    	nameElement.attr('style', 'color:green;');
	    }
	    bodyElement.prepend(msgBodyElement).prepend(avatarElement);

	    messageList.append(bodyElement);
	    //messageList.mCustomScrollbar("update");
  	});
});