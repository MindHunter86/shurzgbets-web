<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBonus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bonus', function ($table) {
            $table->integer('assetid')->default(0)->after('classid');
            $table->dropColumn(array(
                'name',
                'market_hash_name',
                'rarity',
                'price'
            ));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bonus', function ($table) {
            $table->dropColumn('assetid');
            $table->string('name');
            $table->string('market_hash_name');
            $table->string('rarity');
            $table->float('price');
        });
    }
}
