<?php namespace App\Services;

use App\Http\Controllers\GameController;
use App\Http\Controllers\SteamController;
use Cache;
use Exception;

class SteamItem {

    const STEAM_PRICE_URL = 'http://steamcommunity.com/market/priceoverview/?appid=730&currency=5&market_hash_name=';

    public  $classid;
    public  $name;
    public  $market_hash_name;
    public  $price;
    public  $rarity;

    public function __construct($info)
    {
        $this->classid = !isset($info['classid']) ? $info['classId'] : $info['classid'];
        $this->name = $info['name'];
        $this->market_hash_name = $info['market_hash_name'];
        $this->rarity = isset($info['rarity']) ? $info['rarity'] : self::getItemRarity($info);
        if ($price = $this->getItemPrice()) {
            if (isset($price))
                $this->price = $price;
        }else{
            $this->_setToFalse();
        }
    }

    public function getItemPrice() {
        if (Cache::has('steam_market_prices')) {
            $prices = Cache::get('steam_market_prices');
            return array_key_exists($this->market_hash_name, $prices) ? $prices[$this->market_hash_name] : false;
        }
        return false;
    }

    public static function getItemPriceFromSteam($marketHashName) {
        try{
            $json = file_get_contents(self::STEAM_PRICE_URL . urlencode($marketHashName));
            $json = json_decode($json, true);
            if($json['success']) {
                if(isset($json['lowest_price']))
                    return floatval(str_replace(',', '.', substr($json['lowest_price'], 0, -7)));
                else
                    return floatval(str_replace(',', '.', substr($json['median_price'], 0, -7)));
            }
            else
                return false;
        }catch(Exception $e){
            echo $e->getMessage();
            return false;
        }
    }
/*
    public function getItemInfo() {
        $json = file_get_contents(sprintf(self::STEAM_ITEM_URL, SteamController::steamApiKey, $this->classid));
        $json = json_decode($json, true);
        if($json["result"]['success'])
            return (object) $json["result"][$this->classid];
        else
            return false;
    }
*/

    public static function getItemRarity($info) {
        $type = $info['type'];
        $rarity = '';

        $types = array("StatTrak™ "," Pistol", " SMG", " Rifle", " Shotgun", " Sniper Rifle", " Machinegun", " Container", " Knife", " Sticker", " Music Kit", " Key", " Pass", " Gift", " Tag", " Tool");
        $typesrep = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
        $type = str_replace($types, $typesrep, $type);

        switch ($type) {
            case 'Mil-Spec Grade':
            case 'High Grade':
                $rarity = 'milspec'; break;
            case 'Restricted':
            case 'Remarkable':
                $rarity = 'restricted'; break;
            case 'Classified':
            case 'Exotic':
                $rarity = 'classified'; break;
            case 'Covert':
                $rarity = 'covert'; break;
            case 'Consumer Grade':
            case 'Base':
            case 'Industrial Grade':
                $rarity = 'common'; break;
            case '★':
                $rarity = 'rare'; break;
        }

        return $rarity;
    }

    private function _setToFalse()
    {
        $this->name = false;
        $this->price = false;
        $this->rarity = false;
    }
}