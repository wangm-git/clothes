<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberShop extends Model
{
    protected $table = 'clothes_member_shop';

    public $timestamps=false;

    public function hasManyShops()
	{
		return $this->hasMany('Shop', 'member', 'id');
	}
}
