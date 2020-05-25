<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'clothes_order';

    public $timestamps=false;

    public function orderInfo()
	{
	  return $this->hasMany('App\OrderInfo', 'oid', 'id');
	}
}
