<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferalTransaction extends Model
{
    protected $table = 'referal_transactions';

    protected $fillable = ['referal_items', 'tradeId', 'gainer_id', 'total_price'];

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne('App\User','id','gainer_id');
    }
}
