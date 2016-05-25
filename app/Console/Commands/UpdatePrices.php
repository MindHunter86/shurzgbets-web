<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Services\SteamItem;
use App\Item;
use Cache;

class UpdatePrices extends Command
{
    const DEFAULT_USDRUB = 67;
    private $USDRUB;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:priceupdate {--instant} {--fillinfo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update prices of items from Steam Market';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->log("Updating USDRUB course");
        $this->updateUSDRUB();
        $isInstant = $this->option('instant');
        $onlyInfo = $this->option('fillinfo');
        if ($isInstant || !$onlyInfo) {
            $this->log('Start price loading');
            if ($isInstant)
                $this->log('Update applied instantly');
            $this->parseMarket($tmpPrices);
            $updated = 0;
            $created = 0;
            foreach (array_keys($tmpPrices) as $itemname) {
                    $price = round($tmpPrices[$itemname]*$this->USDRUB,2);
                    $item = Item::where('market_hash_name', $itemname)->first();
                    if (is_null($item)) {
                        Item::create(array(
                            "name"=>$itemname,
                            "market_hash_name"=>$itemname,
                            "price"=>$price
                        ));
                        $this->log("Price for {$itemname} ~{$price} added [NEW!]");
                        $created++;
                    } elseif ($isInstant && $item->price != $price && $price>0) {
                        $item->price = $price;
                        $item->save();
                        $this->log("Price for {$itemname} ~{$price} updated");
                        $updated++;
                    }
            }
            Cache::forever('steam_market_prices', $tmpPrices);
            $this->log("Market parsed. Total updated items: {$updated}. New items: {$created}");
            return true;
        }

        if ($onlyInfo) {
            $this->log('Start info loading');
            $items = Item::where('classid',0)->get();
            foreach ($items as $item) {
                $newItem = $this->getInfo($item->market_hash_name);
                if ($newItem) {
                    $item->rarity = $newItem['rarity'];
                    $item->classid = $newItem['classid'];
                    $item->save();
                    $this->log("[INFO] {$item->market_hash_name} parsed");
                } else {
                    $this->log("Can't get info for {$item->market_hash_name}");
                }
                sleep(15);
            }
            $this->log('Update info finished');
        }
    }

    private function getInfo($marketName) {
        $link = sprintf('http://steamcommunity.com/market/listings/730/%s',rawurlencode($marketName));
        $strpage = file_get_contents($link);
        $item = false;
        if (preg_match('%"\d+":({"currency".*?"descriptions":\[.*?\].*?"owner":\d+})%s',$strpage,$result)) {
            $info = json_decode($result[1]);
            $item = [];
            $item['name']=$info->market_hash_name;
            $item['market_hash_name']=$info->market_hash_name;
            $item['classid']=$info->classid;
            $item['type']=$info->type;
            $item['rarity']=SteamItem::getItemRarity($item);
        }
        return $item;

    }

    private function parseMarket(&$items) {
        //Code from hellstore.net
        $from = 35;
        $to = 500000;
        $dir = 'desc';
        $page = 0;
        for ($k = (int)$from; $k <= (int)$to; $k++) {
            if ($k == 0) {
                $lol = 0;
            } else {
                $lol = ((int)$k * 100) + 1;
            }

            $link = "http://steamcommunity.com/market/search/render/?query=&start={$lol}&currency=5&count=100&search_descriptions=0&sort_column=price&sort_dir=$dir&appid=730";
            $strpage = file_get_contents($link);
            $json = json_decode($strpage);

            $sdata = $json->results_html;
            $total_count = $json->total_count;
            $totalPages = floor($total_count / 100)+1;
            $this->log("Parsing market list page ".++$page." of ".$totalPages);

            preg_match_all('%<a class="market_listing_row_link" href="(.+?)" id="resultlink.*?<span class="normal_price">(.+?) .+?</span>.+?<span class="sale_price">(.+?) .+?</span>.*?class="market_listing_item_name" style=".*?">(.+?)</span>%s', $sdata, $result, PREG_PATTERN_ORDER);
            for ($i = 0; $i < count($result[0]); $i++) {
                $steam_price_sale = substr($result[3][$i],1);
                $steam_market_name = $result[4][$i];

                $steam_price_sale = str_replace(",", ".", $steam_price_sale);
                $items[$steam_market_name] = $steam_price_sale;
            }

            sleep(30);
            //$total_count = $json->total_count;
            if (!is_int($total_count)) {
                if (preg_match('/total_count":(.+?),"/', $strpage, $regs)) {
                    $total_count = $regs[1];
                }
            }
            if ($total_count < $lol) {
                return;
            }
        }
    }

    Public function getSteamMarket($url)
    {
        $headers = array(
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-us,en',
            'Connection: keep-alive',
            'Accept-Encoding: gzip, deflate, sdch',
            'User-Agent: Mozilla/4.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.2272.101 Safari/537.36',
        );

        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, array(
                CURLOPT_VERBOSE => false,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_URL => $url,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true
            ));
            $response = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            return 'Ex: ' . $e->getMessage();
        }

        return $response;
    }

    private function log($text) {
        echo sprintf("[%s] %s\r\n", Carbon::now()->toDayDateTimeString(), $text);
    }

    private function updateUSDRUB() {
        try {
            $json = json_decode(file_get_contents('https://query.yahooapis.com/v1/public/yql?q=select+*+from+yahoo.finance.xchange+where+pair+=+"USDRUB"&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys'));
            $this->USDRUB = $json->query->results->rate->Rate;
        } catch (Exception $e) {
            $this->USDRUB = self::DEFAULT_USDRUB;
        }
    }
}
