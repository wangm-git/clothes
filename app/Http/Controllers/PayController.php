<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

// use Yansongda\Pay\Pay;
use App\Order;
use App\OrderInfo;
use App\Cart;
use App\Shop;
use App\MemberShop;
use App\Specifications;
use App\MemberSpe;
use App\Address;
use App\Postage;
use Pay;

use Carbon\Carbon;

class PayController extends Controller
{
    public function placeOrder(Request $request)
    {
        $postage = Postage::findOrFail(1);
        $memberSpe = MemberSpe::find($request->mspeid);
        $shop = Shop::find($request->shopid);
        $code = $this->createCode();

        if($request->num > $memberSpe->num){
            return json_encode(['code'=>'400','data' => '下单失败，库存不足']);            
        }
        
        DB::beginTransaction();
        try {
            $order = new Order;
            $order->user = $request->user;
            $order->member = $request->member;
            // $order->name = $address->name;
            // $order->phone = $address->phone;
            $order->title = $shop->title;
            $order->Postage = $postage->pay;
            $order->pay = $memberSpe->pay * $request->num + $postage->pay;
            // $order->address = $address->province.$address->city.$address->area.$address->address;
            $order->order_no = $code;
            $order->over = 0;
            $order->date = now();
            $order->save();

            $orderInfo = new OrderInfo;
            $orderInfo->shopid = $request->shopid;
            $orderInfo->speid = $request->speid;
            $orderInfo->over = 0;
            $orderInfo->num = $request->num;
            $orderInfo->pay = $memberSpe->pay;
            $orderInfo->mshopid = $request->mshopid;
            $orderInfo->mspeid = $request->mspeid;
            $orderInfo->oid = $order->id;
            $orderInfo->date = now();
            $orderInfo->save();

            DB::commit();
            return json_encode(['code' => '200', 'order_no' =>$code]);
        } catch (Exception $e) {
            DB::rollBack();
            return json_encode(['code' => '400', 'data' =>'下单失败']);
        }
    }

    public function placeOrderFromCart(Request $request)
    {
        $code = $this->createCode();
        $postage = Postage::findOrFail(1);

        DB::beginTransaction();
        try {
            $order = new Order;
            $order->user = $request->user;
            $order->member = $request->member;
            // $order->name = $address->name;
            // $order->phone = $address->phone;
            $order->title = '商品类集合';
            $order->pay = 0;
            $order->Postage = $postage->pay;
            // $order->address = $address->province.$address->city.$address->area.$address->address;
            $order->order_no = $code;
            $order->over = 0;
            $order->date = now();
            $order->save();

            $total = 0;
            $carts = explode(',', $request->carts);
            foreach ($carts as $key => $cart) {
                $cartData = Cart::find($cart);
                $memberSpe = MemberSpe::find($cartData->member_spe_id);

                if($cartData->num > $memberSpe->num){
                    return json_encode(['code'=>'400','data' => '下单失败，库存不足']);
                }

                $orderInfo = new OrderInfo;
                $orderInfo->shopid = $cartData->shop_id;
                $orderInfo->speid = $cartData->spe_id;
                $orderInfo->over = 0;
                $orderInfo->num = $cartData->num;
                $orderInfo->pay = $memberSpe->pay;
                $orderInfo->mshopid = $cartData->member_shop_id;
                $orderInfo->mspeid = $cartData->member_spe_id;
                $orderInfo->oid = $order->id;
                $orderInfo->date = now();
                $orderInfo->save();

                $total += $memberSpe->pay * $cartData->num;
                $cartData->delete_at = 1;
                $cartData->save();
            }

            $order->pay = $total;
            $order->save();
            DB::commit();
            return json_encode(['code' => '200', 'order_no' =>$code]);
        } catch (Exception $e) {
            DB::rollBack();
            return json_encode(['code' => '400', 'data' =>'下单失败']);
        }
    }

    public function createCode()
    {
        $code = date('Ymdhis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        $order = Order::where('order_no', $code)->first();
        if (empty($order)) {
            return $code;
        } else {
            $this->createCode();
        }
        
    }

    public function show(Request $request)
    {
        $order = Order::where('order_no', $request->order_no)
                        ->with('orderInfo')
                        ->first();

        if (!empty($order)) {
            $address = Address::where('member_id', $request->user_id)->where('is_default', 1)->first();

            $orderData = [];
            foreach ($order->orderinfo as $key => $orderinfo) {
                $data = OrderInfo::where('clothes_orderinfo.id', $orderinfo->id)
                                    ->join('clothes_shop', 'clothes_shop.id', '=', 'clothes_orderinfo.shopid')
                                    ->join('clothes_member_spe', 'clothes_member_spe.id', '=', 'clothes_orderinfo.mspeid')
                                    ->join('clothes_Specifications', 'clothes_Specifications.id', '=', 'clothes_orderinfo.speid')
                                    ->select('clothes_shop.title', 'clothes_shop.pic', 'clothes_member_spe.pay', 'clothes_Specifications.colour', 'clothes_Specifications.size', 'clothes_orderinfo.num')
                                    ->first();
                $picArr = explode(',', $data->pic);
                $data->pic = env('PIC_URL').$picArr[0];

                $orderData[] = $data;
            }

            return json_encode(['code' => 200, 'data' => $orderData, 'total' => $order->pay, 'postage'=> $order->Postage,'address'=>$address]);
        } else {
            return [];
        }
        
    }

    public function pay(Request $request)
    {
        $order = Order::where('order_no', $request->order_no)->first();
        $address = Address::find($request->address_id);

        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->address = $address->province.$address->city.$address->area.$address->address;
        $order->save();

        $wechat = [
            'out_trade_no' => $request->order_no,
            'body' => $order->title,
            'total_fee' => $order->pay * 100,
            'openid' => $request->open_id,
        ];

        // $wechat = [
        //     'out_trade_no' => '222222323232323',
        //     'body' => 'test',
        //     'total_fee' => $order->pay * 100,
        //     'openid' => $request->open_id,
        // ];

        $result = Pay::wechat()->miniapp($wechat);
        return json_encode($result);
    }

    public function notify()
    {
        $pay = Pay::wechat(config('pay.wechat'));

        try{
            $data = $pay->verify(); //验签
            
            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success();// laravel 框架中请直接 `return $pay->success()`
    }
}
