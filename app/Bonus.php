<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
	protected $table = 'bonus';
    protected $fillable = ['classid', 'assetid'];

    public function item() {
        return $this->hasOne('App\Item','classid','classid');
    }
}
