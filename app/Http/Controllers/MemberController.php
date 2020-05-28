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

        if ($request->orderby == 'pay_asc') {
            $orderby = 'pay';
            $sort = 'asc';
        } else {
            $orderby = $request->orderby;
            $sort = 'desc';
        }

        $memberShop = MemberShop::where('member', $request->id)
                        ->where('clothes_member_shop.over', 1)
                        ->join('clothes_shop', 'clothes_member_shop.shopid', '=', 'clothes_shop.id')
                        ->select('clothes_shop.title', 'clothes_shop.pic', 'clothes_member_shop.pay', 'clothes_member_shop.sale', 'clothes_member_shop.id as m_shopid')
                        ->orderBy('clothes_member_shop.'.$orderby, $sort)
                        ->skip($request->skip)
                        ->take(8)
                        ->get();

        $shopData = [];
        $picArr = [];
        foreach ($memberShop as $key => $shop) {
            $shopData[$key]['title'] = $shop->title;
            $picArr = explode(',', $shop->pic);
            $shopData[$key]['pic'] = env('PIC_URL') . $picArr[0];
            $shopData[$key]['pay'] = $shop->pay;
            $shopData[$key]['sale'] = $shop->sale;
            $shopData[$key]['m_shopid'] = $shop->m_shopid;
        }

        $hotShop = MemberShop::where('member', $request->id)
                        ->where('clothes_member_shop.over', 1)
                        ->join('clothes_shop', 'clothes_member_shop.shopid', '=', 'clothes_shop.id')
                        ->select('clothes_shop.title', 'clothes_shop.pic', 'clothes_member_shop.pay', 'clothes_member_shop.sale', 'clothes_member_shop.id as m_shopid')
                        ->orderBy('clothes_member_shop.sale', 'desc')
                        ->take(4)
                        ->get();

        $hotData = [];
        $picArr = [];
        foreach ($hotShop as $key => $shop) {
            $hotData[$key]['title'] = $shop->title;
            $picArr = explode(',', $shop->pic);
            $hotData[$key]['pic'] = env('PIC_URL') . $picArr[0];
            $hotData[$key]['pay'] = $shop->pay;
            $hotData[$key]['m_shopid'] = $shop->m_shopid;
        }

        return json_encode(['code'=>'200', 'data'=>['shopname' => $member->title, 'shopabout' => $member->about, 'Templateid' => $member->Templateid, 'shop' => $shopData, 'hot'=> $hotData]]);
    }

    public function getTypeInfo(Request $request)
    {
        $member = Member::find($request->id);

        if ($request->orderby == 'pay_asc') {
            $orderby = 'pay';
            $sort = 'asc';
        } else {
            $orderby = $request->orderby;
            $sort = 'desc';
        }

        $memberShop = MemberShop::where('member', $request->id)
                        ->where('clothes_member_shop.over', 1)
                        ->where('clothes_shop.type', $request->type)
                        ->join('clothes_shop', 'clothes_member_shop.shopid', '=', 'clothes_shop.id')
                        ->select('clothes_shop.title', 'clothes_shop.pic', 'clothes_member_shop.pay', 'clothes_member_shop.sale', 'clothes_member_shop.id as m_shopid')
                        ->orderBy('clothes_member_shop.'.$orderby, $sort)
                        ->skip($request->skip)
                        ->take(8)
                        ->get();

        $shopData = [];
        $picArr = [];
        foreach ($memberShop as $key => $shop) {
            $shopData[$key]['title'] = $shop->title;
            $picArr = explode(',', $shop->pic);
            $shopData[$key]['pic'] = env('PIC_URL') . $picArr[0];
            $shopData[$key]['pay'] = $shop->pay;
            $shopData[$key]['sale'] = $shop->sale;
            $shopData[$key]['m_shopid'] = $shop->m_shopid;
        }

        return json_encode(['code'=>'200', 'data'=>$shopData]);
    }
}
