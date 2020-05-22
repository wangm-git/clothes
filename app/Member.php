<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'clothes_member';

    public function hasManyMemberShops()
	{
		return $this->hasMany('MemberShop', 'member', 'id');
	}
}
