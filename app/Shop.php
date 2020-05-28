<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'clothes_shop';

    public $timestamps=false;

 //    public function hasManyMemberShops()
	// {
	// 	return $this->hasMany('MemberShop', 'shopid', 'id');
	// }
}
