<?
return [
	'title' => '<b>ABOUT</b>',
	'content' => '
<div class="user-winners-table" style="color: #eaeaea; line-height: 17px; text-shadow: none;">
<div style="margin-bottom: 10px; border-left: 1px solid #FFBD4C; padding-left: 6px;">
<span style="color: #FFBD57;">SHURZGBETS</span> – a Service in which participating bring your items (skins) and when dialed in the amount of 100 items or it takes 2 minute after second Deposit, the system 1 determines the winner, which gets all items.<br>
The winner is randomly determined, the chance of winning depends on the amount of paid skins.
</div>

<div style="margin-bottom: 10px; padding: 5px 6px; border: 1px solid #5cb85c;">
<span style="text-transform: uppercase; color: #80DB80;">the Principle is simple:</span> the larger and more expensive items You bet, the more chance to hit the jackpot! But even putting 1 rouble, You have the opportunity to hit the jackpot!
</div>

<div style="margin-bottom: 10px; border-left: 1px solid #60B3E5; padding-left: 6px; line-height: 16px;">
<span style="color: #60B3E5; padding-bottom: 5px; text-transform: uppercase;">How it works:</span><br>
 <div style="margin-bottom: 8px; margin-top: 2px; padding-left: 10px;">
1: <span style="">You make your own items using the button "participate" by sending a trade to our bot.<br>
You can enter a maximum of {{ \App\Http\Controllers\GameController::MAX_ITEMS }} skins at a time, the total amount of which cannot be less than {{ \App\Http\Controllers\GameController::MIN_PRICE }}p</span>
</div>
<div style="margin-bottom: 8px; padding-left: 10px;">
 2: We translate the items entered in points in accordance with their price. For each 1 cent the value of items you get 1 point (1 ruble - 100 points)<br>
Chance of winning depends on the number of points. The more items you make, the higher your chance of winning.
</div>
<div style="padding-left: 10px;">
 3: When reaching the threshold of 100 skins (or 2 minutes since the second Deposit), we collect all the issued points together and randomly select one winner, but in priority to those participants who have more points than the rest.<br>
The winner gets all items listed at the end of the round.
</div>
</div>

<div style="margin-bottom: 10px;padding-left: 6px;line-height: 18px;border: 1px solid #EC785D;">
<div style="color: #EC785D; padding-top: 5px; text-transform: uppercase;">Rules and features:</div>
 <ol style="padding: 0px 30px; margin: 3px; line-height: 15px; font-size: 13px;">
<li style="padding-bottom: 6px;">a Maximum Deposit of items - 20 pieces per trade. There are no upper limits to the price for the item, the Deposit may begin with a total amount of at least {{ \App\Http\Controllers\GameController::MIN_PRICE }}R.</li>
 <li style="padding-bottom: 6px;">If any item be entered in the “Bank” on a recent Deposit and the count of items passes a certain threshold, they will be considered in the current round, since the user has made their participation in current and not in the previous round.</li>
<li style="padding-bottom: 6px;">For site development and carrying out of competitions, we charge a Commission on each game - {{ \App\Http\Controllers\GameController::COMMISSION }}% of all things of the game.</li>
 <li style="padding-bottom: 6px;">Deposits and withdrawal of the prize Fund occur very rapidly (depending on the workload of the bot and the Steam servers)</li>
<li style="padding-bottom: 6px;">Each time sending the items, You agree to the terms of use.</li>
<li style="padding-bottom: 6px;">If Your inventory is closed, Your winnings will not be sent.</li>
 <li style="padding-bottom: 6px;">things are Accepted only for CS:GO, other things will be taken, but will not be counted on the website. So we can guarantee the correct valuation of things only when it is on the Steam market, otherwise your subject may be improperly appreciated.</li>
<li style="padding-bottom: 6px;">it is Forbidden to put gift items and gift sets, such trades are cancelled.</li>
 <li style="padding-bottom: 6px;">You have the guarantee of receiving your items within half an hour after closing the pool. After this time we are not responsible for lost items.</li>
<li style="padding-bottom: 6px;">If you cancel the exchange or sent a counter-proposal after the victory, your belongings returned to you will not, because the bot is not designed for resubmission of things</li>
<li style="padding-bottom: 6px;">If our bot was banned within 30 minutes from the end of the match, we will refund only your bid, not the winning.</li>
 <li style="padding-bottom: 6px;">If you put in 30 seconds before the end of the match, then there is a possibility that your skins will get to the next game. We are not responsible for: steam does not always process exchanges instantly</li>
</ol>
</div>
</div>
	'
];