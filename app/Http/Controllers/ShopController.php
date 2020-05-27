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
        $shop = MemberShop::where('clothes_member_shop.id', $request->id)
                            ->where('clothes_member_shop.over', 1)
                            ->join('clothes_shop', 'clothes_member_shop.shopid', '=', 'clothes_shop.id')
                            ->select('clothes_shop.title', 'clothes_shop.pic', 'clothes_shop.con','clothes_shop.relation','clothes_member_shop.pay', 'clothes_member_shop.sale', 'clothes_member_shop.speid','clothes_member_shop.member', 'clothes_member_shop.id', 'clothes_member_shop.shopid')
                            ->first();

        if (empty($shop)){
            return ['data'=>'数据不存在'];
        }

        $picArr = explode(',', $shop->pic);
        foreach ($picArr as $key => $value) {
            $picArr[$key] = env('PIC_URL').$value;
        }

        $spe = MemberSpe::where('clothes_member_spe.shopid', $shop->id)
                            ->where('clothes_member_spe.member', $shop->member)
                            ->join('clothes_Specifications', 'clothes_Specifications.id', '=', 'clothes_member_spe.speid')
                            ->select('clothes_Specifications.colour', 'clothes_Specifications.size', 'clothes_member_spe.pay', 'clothes_member_spe.id as member_spe_id', 'clothes_Specifications.id as spe_id','clothes_member_spe.num')
                            // ->groupBy('clothes_specifications.colour')
                            ->get();

        $speData = [];
        $colour = [];
        foreach ($spe as $key => $value) {
            if(!in_array($value->colour, $colour)){
                $colour[] = $value->colour;
            }
            $speData[$value->colour]['colour'] = $value->colour;
            $speData[$value->colour]['data'][] = ['size'=>$value->size, 'pay'=>$value->pay, 'spe_id' => $value->spe_id, 'member_spe_id' => $value->member_spe_id, 'num'=>$value->num];
        }
        $speData = array_values($speData);
        $likeShop = MemberShop::where('clothes_shop.title', 'like', '%'.$shop->title.'%')
                            ->where('clothes_member_shop.member', $shop->member)
                            ->where('clothes_member_shop.id', '!=',$shop->id)
                            ->join('clothes_shop', 'clothes_member_shop.shopid', '=', 'clothes_shop.id')
                            ->select('clothes_shop.title', 'clothes_shop.pic', 'clothes_member_shop.id','clothes_member_shop.pay', 'clothes_member_shop.sale')
                            ->take(4)
                            ->get();

        $likePic = [];
        foreach ($likeShop as $key => $like) {
            $likePic = explode(',', $like->pic);
            $like->pic = env('PIC_URL').$likePic[0];
        }

        if (!empty($shop->relation)) {
            $relationIds = explode(',', $shop->relation);

            $relationShop = MemberShop::whereIn('clothes_member_shop.shopid', $relationIds)
                                ->where('clothes_member_shop.member', $shop->member)
                                ->join('clothes_shop', 'clothes_member_shop.shopid', '=', 'clothes_shop.id')
                                ->select('clothes_shop.title', 'clothes_shop.pic', 'clothes_member_shop.pay','clothes_member_shop.id', 'clothes_member_shop.sale')
                                ->take(5)
                                ->get();

            $relationPic = [];
            foreach ($relationShop as $key => $relation) {
                $relationPic = explode(',', $relation->pic);
                $relation->pic = env('PIC_URL').$relationPic[0];
            }
        }else{
            $relationShop = [];
        }

        return json_encode(['shopid'=>$shop->shopid,'title' => $shop->title, 'pic' => $picArr,'con' => $shop->con,'pay'=>$shop->pay,'sale'=>$shop->sale,'spe' => $speData, 'like' => $likeShop, 'relation'=>$relationShop]);
    }
}
