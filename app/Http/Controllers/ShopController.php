<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MemberShop;
use App\Shop;
use App\MemberSpe;
use App\Specifications;

class ShopController extends Controller
{
    public function show(Request $request)
    {
        $shop = MemberShop::where('member', $request->member)
                            ->where('shopid', $request->shopid)
                            ->where('clothes_member_shop.over', 1)
                            ->join('clothes_shop', 'clothes_member_shop.shopid', '=', 'clothes_shop.id')
                            ->select('clothes_shop.title', 'clothes_shop.pic', 'clothes_shop.con','clothes_member_shop.pay', 'clothes_member_shop.sale', 'clothes_member_shop.speid','clothes_member_shop.member', 'clothes_member_shop.id')
                            ->first();

        $picArr = explode(',', $shop->pic);
        $speArr = explode(',', $shop->speid);

        $spe = MemberSpe::whereIn('clothes_member_spe.id', $speArr)
                            ->join('clothes_specifications', 'clothes_specifications.id', '=', 'clothes_member_spe.speid')
                            ->select('clothes_specifications.colour', 'clothes_specifications.size', 'clothes_member_spe.pay', 'clothes_member_spe.id as member_spe_id', 'clothes_specifications.id as spe_id')
                            // ->groupBy('clothes_specifications.colour')
                            ->get();

        $speData = [];
        $colour = [];
        foreach ($spe as $key => $value) {
            if(!in_array($value->colour, $colour)){
                $colour[] = $value->colour;
            }
            $speData[$value->colour][] = ['size'=>$value->size, 'pay'=>$value->pay, 'spe_id' => $value->spe_id, 'member_spe_id' => $value->member_spe_id];
        }

        $likeShop = MemberShop::where('clothes_shop.title', 'like', '%'.$shop->title.'%')
                            ->where('clothes_member_shop.member', $shop->member)
                            ->where('clothes_member_shop.id', '!=',$shop->id)
                            ->join('clothes_shop', 'clothes_member_shop.shopid', '=', 'clothes_shop.id')
                            ->select('clothes_shop.title', 'clothes_shop.pic')
                            ->take(4)
                            ->get();

        return json_encode(['title' => $shop->title, 'pic' => $picArr,'con' => $shop->con,'pay'=>$shop->pay,'sale'=>$shop->sale,'spe' => $speData, 'like' => $likeShop]);
    }
}
