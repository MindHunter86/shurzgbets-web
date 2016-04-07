<?php

get('/login', ['as' => 'login', 'uses' => 'SteamController@login']);
get('/', ['as' => 'index', 'uses' => 'GameController@currentGame']);
get('/about', ['as' => 'about', 'uses' => 'PagesController@about']);
get('/top', ['as' => 'top', 'uses' => 'PagesController@top']);
get('/support', ['as' => 'support', 'uses' => 'PagesController@support']);
get('/game/{game}', ['as' => 'game', 'uses' => 'PagesController@game']);
post('ajax', ['as' => 'ajax', 'uses' => 'AjaxController@parseAction']);
get('/history', ['as' => 'history', 'uses' => 'PagesController@history']);
get('/shop', ['as' => 'shop', 'uses' => 'ShopController@index']);
get('/payment', 'DonateController@payment');  
get('/lang/{lang}', ['as'=>'lang.switch', 'uses'=>'LanguageController@switchLang']);
Route::group(['middleware' => 'auth'], function () {
    post('/merchant', 'DonateController@merchant'); 
    post('/ajax/chat', 'AjaxController@chat'); 
    get('/pay/success', 'DonateController@success');  
    get('/pay/fail', 'DonateController@fail');  
    get('/deposit', ['as' => 'deposit', 'uses' => 'GameController@deposit']);
    post('/shop/buy', ['as' => 'shop.buy', 'uses' => 'ShopController@buyItem']);
    get('/shop/history', ['as' => 'shop.history', 'uses' => 'ShopController@history']);
    get('/shop/admin', ['as' => 'shop.admin', 'uses' => 'ShopController@admin', 'middleware' => 'access:admin']);
    get('/settings', ['as' => 'settings', 'uses' => 'PagesController@settings']);
    post('/settings/save', ['as' => 'settings.update', 'uses' => 'SteamController@updateSettings']);
    get('/history/profile', ['as' => 'myhistory', 'uses' => 'PagesController@profile_history']);
    get('/logout', ['as' => 'logout', 'uses' => 'SteamController@logout']);
    post('/addTicket', ['as' => 'add.ticket', 'uses' => 'GameController@addTicket']);
    post('/getBalance', ['as' => 'get.balance', 'uses' => 'GameController@getBalance']);

    //рефералка
    post('/promo/create', 'ReferalController@create');
    post('/promo/accept', 'ReferalController@accept');
    get('/referals/stats', ['as' => 'promo', 'uses' => 'PagesController@promo']);
    get('/referals', ['as' => 'promo', 'uses' => 'PagesController@promoSettings']);

    post('/giveaway/accept', 'GameController@acceptLottery');
    get('/giveaway/history', ['as' => 'giveaway', 'uses' => 'PagesController@giveaway']);
    get('/giveaway', ['as' => 'giveaway', 'uses' => 'PagesController@lottery']);

});
Route::group(['prefix' => 'admin','middleware' => 'access' ], function () {
    get('/', ['uses' => 'AdminController@index']);
    get('/history', ['uses' => 'AdminController@history']);
    get('/history/{game}', ['uses' => 'AdminController@game']);
    get('/shop', ['uses' => 'AdminController@shop']);
    get('/send', ['uses' => 'AdminController@send']);
    post('/send/ajax', 'AdminController@sendAjax');
    post('/send/ajaxShop', 'AdminController@sendshopAjax');
    get('/newLottery', 'GameController@newLottery');
});

Route::group(['prefix' => 'api', 'middleware' => 'secretKey'], function () {
    post('/userqueue', 'GameController@userqueue');
    post('/checkOffer', 'GameController@checkOffer');
    post('/newBet', 'GameController@newBet');
    post('/setGameStatus', 'GameController@setGameStatus');
    post('/setPrizeStatus', 'GameController@setPrizeStatus');
    post('/getCurrentGame', 'GameController@getCurrentGame');
    post('/getWinners', 'GameController@getWinners');
    post('/getPreviousWinner', 'GameController@getPreviousWinner');
    post('/newGame', 'GameController@newGame');
    post('/bonusBet', 'GameController@bonusBet');
    post('/clear', 'GameController@clearSuck');
    post('/shop/newItems', 'ShopController@addItemsToSale');
    post('/shop/setItemStatus', 'ShopController@setItemStatus'); 

    post('/newLottery', 'GameController@newLottery');
    post('/getWinnersLottery', 'GameController@getWinnersLottery');
});
