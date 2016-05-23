<?php

namespace App\Console\Commands;

use App\Services\CsgoFast;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Item;
use Cache;
use Exception;

class UpdatePricesFast extends Command
{
    const DEFAULT_USDRUB = 67;
    private $USDRUB;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csgofast:priceupdate {--instant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update prices of items from CSGO Fast';

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
        $this->log('Start price loading');
        $isInstant = $this->option('instant');
        if ($isInstant)
            $this->log('Update applied instantly');
        $items = Item::all();
        $cacheItems = [];
        try {
            $this->log("Try get prices from api.csgofast.com");
            $jsonItems = file_get_contents('https://api.csgofast.com/price/all');
            $itemPrices = json_decode($jsonItems);
        } catch (Exception $e){
            $this->log("Error: {$e->getMessage()}");
            return false;
        }

        foreach ($items as $item) {
            if (isset($itemPrices->{$item->market_hash_name})) {
                $price = round($itemPrices->{$item->market_hash_name}*$this->USDRUB, 2);
                $cacheItems[$item->market_hash_name] = $price;
                $this->log("Price for {$item->market_hash_name} ~{$price}");
                if ($isInstant && $item->price != $price) {
                    $item->price = $price;
                    $item->save();
                }
            } else {
                $this->log("Can't get price for {$item->market_hash_name}");
            }
        }
        Cache::forever('csgofast_prices',$cacheItems);
        $this->log('Update finished');
        $this->log('USDRUB: '.$this->USDRUB);

        return true;
    }

    private function updateUSDRUB() {
        try {
            $json = json_decode(file_get_contents('https://query.yahooapis.com/v1/public/yql?q=select+*+from+yahoo.finance.xchange+where+pair+=+"USDRUB"&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys'));
            $this->USDRUB = $json->query->results->rate->Rate;
        } catch (Exception $e) {
            $this->USDRUB = self::DEFAULT_USDRUB;
        }
    }

    private function log($text) {
        echo sprintf("[%s] %s\r\n", Carbon::now()->toDayDateTimeString(), $text);
    }
}
