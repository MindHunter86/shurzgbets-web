<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferalTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referal_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('referal_items');
            $table->integer('tradeId')->unsigned();
            $table->integer('gainer_id')->unsigned();
            $table->foreign('gainer_id')->references('id')->on('users')->onDelete('cascade');
            $table->float('total_price');
            $table->timestamp('sended_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('referal_transactions');
    }
}
