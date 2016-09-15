<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Ticket::truncate();
        \App\Ticket::create([
            'name' => 'Карточка на 100 руб',
            'img'  => '/shurzg/images/cart/c1.png',
            'price' => 100
        ]);
        \App\Ticket::create([
            'name' => 'Карточка на 300 руб',
            'img'  => '/shurzg/images/cart/c2.png',
            'price' => 300
        ]);
        \App\Ticket::create([
            'name' => 'Карточка на 600 руб',
            'img'  => '/shurzg/images/cart/c3.png',
            'price' => 600
        ]);
        \App\Ticket::create([
            'name' => 'Карточка на 900 руб',
            'img'  => '/shurzg/images/cart/c4.png',
            'price' => 900
        ]);
        \App\Ticket::create([
            'name' => 'Карточка на 1800 руб',
            'img'  => '/shurzg/images/cart/c5.png',
            'price' => 1800
        ]);
        \App\Ticket::create([
            'name' => 'Карточка на 3600 руб',
            'img'  => '/shurzg/images/cart/c6.png',
            'price' => 3600
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Ticker::truncate();
    }
}
