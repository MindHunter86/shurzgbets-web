$(document).ready(function(){
	var cookie = $.cookie('language');
	if (cookie == 'active') {
		$('.language_ico').addClass('active');
	}
});

function language() {
	var lang = $('.language_ico').hasClass('active');
	if(lang) {
		$('.language_ico').removeClass('active');
		$.cookie('language', null);
	} else {
		$('.language_ico').addClass('active');
		$.cookie('language', 'active');
	}
}

function chat() {
	var chat = $('.chat').hasClass('active');
	if(chat) {
		$('.chat').removeClass('active').animate({right: '-334px'}, 300);
	} else {
		$('.chat').addClass('active').animate({right: '0'}, 600);
	}
}