<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Services\SteamItem;
use App\Item;
use Cache;

class UpdatePrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:priceupdate {--instant}';

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
        $this->log('Start price loading');
        $isInstant = $this->option('instant');
        if ($isInstant)
            $this->log('Update applied instantly');
        $items = Item::all();
        $itemPrices = [];

        foreach ($items as $item) {
            if ($price = SteamItem::getItemPriceFromSteam($item->market_hash_name)) {
                $itemPrices[$item->market_hash_name] = $price;
                $this->log("Price for {$item->market_hash_name} ~{$price}");
                if ($isInstant && $item->price != $price) {
                    $item->price = $price;
                    $item->save();
                }
            }
        }
        Cache::forever('steam_market_prices',$itemPrices);
        $this->log('Update finished');
    }

    private function log($text) {
        echo sprintf("[%s] %s\r\n", Carbon::now()->toDayDateTimeString(), $text);
    }
}
