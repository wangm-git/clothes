<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderInfo extends Model
{
    protected $table = 'clothes_orderinfo';

    public $timestamps=false;

    public function orderInfos()
	{
	  return $this->belongsTo('App\Order', 'oid', 'id');
	}
}
