<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Member;
use App\MemberShop;
use App\Shop;

class MemberController extends Controller
{
    public function getInfo(Request $request)
    {
        $member = Member::find($request->id);

        if ($request->orderby == null) {
            $orderby = 'id';
        } else {
            $orderby = $request->orderby;
        }

        $memberShop = MemberShop::where('member', $request->id)
                        ->where('clothes_member_shop.over', 1)
                        ->join('clothes_shop', 'clothes_member_shop.shopid', '=', 'clothes_shop.id')
                        ->select('clothes_shop.title', 'clothes_shop.pic', 'clothes_member_shop.pay', 'clothes_member_shop.sale')
                        ->orderBy('clothes_member_shop.'.$orderby, 'desc')
                        ->skip($request->skip)
                        ->take(8)
                        ->get();

        $shopData = [];
        $picArr = [];
        foreach ($memberShop as $key => $shop) {
            $shopData[$key]['title'] = $shop->title;
            $picArr = explode(',', $shop->pic);
            $shopData[$key]['pic'] = env('APP_URL') . $picArr[0];
            $shopData[$key]['pay'] = $shop->pay;
            $shopData[$key]['sale'] = $shop->sale;
        }
        return json_encode(['code'=>'200', 'data'=>['shopname' => $member->title, 'shopabout' => $member->about, 'shop' => $shopData]]);
    }
}
