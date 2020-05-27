<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Cart;
use App\Shop;
use App\MemberShop;
use App\Specifications;
use App\MemberSpe;

use Carbon\Carbon;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $memberSpe = MemberSpe::findOrFail($request->member_spe_id);

        $oldCart = Cart::where('shop_id', $request->shop_id)->where('member_shop_id', $request->member_shop_id)->where('spe_id', $request->spe_id)->where('member_spe_id', $request->member_spe_id)->where('delete_at', 0)->first();

        if (!empty($oldCart)) {
            $oldCart->num = $request->num + $oldCart->num;
            if ($request->num + $oldCart->num> $memberSpe->num) {
                return json_encode(['code'=>'400','data'=>'数量大于库存']);
            }
            $data = $oldCart->save();
        } else {
            if ($request->num> $memberSpe->num) {
                return json_encode(['code'=>'400','data'=>'数量大于库存']);
            }
            $cart = New Cart;

            $cart->member_id = $request->member_id;
            $cart->user_id = $request->user_id;
            $cart->shop_id = $request->shop_id;
            $cart->member_shop_id = $request->member_shop_id;
            $cart->spe_id = $request->spe_id;
            $cart->member_spe_id = $request->member_spe_id;
            $cart->num = $request->num;
            $cart->date = now();

            $data = $cart->save();
        }

        if ($data == 1){
            return json_encode(['code' => '200', 'data' =>'添加成功']);
        } else{
            return json_encode(['code' => '400', 'data' =>'添加失败']);
        }
    }

    public function update(Request $request)
    {
        $cart = Cart::findOrFail($request->id);
        $memberSpe = MemberSpe::findOrFail($cart->member_spe_id);

        if ($request->num > $memberSpe->num) {
            return json_encode(['code'=>'400','data'=>'数量大于库存']);
        }

        $cart->num = $request->num;
        $data = $cart->save();

        if ($data == 1){
            return json_encode(['code' => '200', 'data' =>'修改成功']);
        } else{
            return json_encode(['code' => '400', 'data' =>'修改失败']);
        }
    }

    public function delete($id)
    {
    	$cart = Cart::find($id);

        $cart->delete_at = 1;
        $data = $cart->save();

        if ($data == 1){
            return json_encode(['code' => '200', 'data' =>'删除成功']);
        } else{
            return json_encode(['code' => '400', 'data' =>'删除失败']);
        }
    }

    public function list(Request $request)
    {
        $cart = Cart::where('user_id', $request->user_id)
                    ->where('delete_at', 0)
                    ->join('clothes_shop', 'clothes_shop.id', '=', 'clothes_cart.shop_id')
                    ->join('clothes_Specifications', 'clothes_Specifications.id', '=', 'clothes_cart.spe_id')
                    ->join('clothes_member_spe', 'clothes_member_spe.id', '=', 'clothes_cart.member_spe_id')
                    ->select('clothes_shop.title', 'clothes_shop.pic', 'clothes_Specifications.colour', 'clothes_Specifications.size', 'clothes_member_spe.pay', 'clothes_cart.num','clothes_cart.over','clothes_cart.id', 'clothes_cart.member_spe_id')
                    ->get();
        $picArr = [];
        foreach ($cart as $key => &$value) {
            $picArr = explode(',', $value->pic);
            $value->pic = env('PIC_URL').$picArr[0];
            $memberSpe = MemberSpe::findOrFail($value->member_spe_id);

            if($value->num > $memberSpe->num){
                $value->status = 1;
            }else{
                $value->status = 0;
            }
        }

        return json_encode($cart);
    }
}
