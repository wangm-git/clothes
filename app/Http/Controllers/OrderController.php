<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderInfo;
use App\OrderRefund;

use Carbon\Carbon;

class OrderController extends Controller
{
    public function list(Request $request)
    {
        $orders = Order::where('user', $request->user_id)
                        ->where('over', $request->over)
                        ->orderBy('id', 'asc')
                        ->select('id', 'over','order_no')
                        ->skip($request->skip)
                        ->take(8)
                        ->get();

        foreach ($orders as $key => $order) {
            $order->orderInfos = OrderInfo::where('oid', $order->id)
                                        ->join('clothes_shop', 'clothes_orderinfo.shopid', '=', 'clothes_shop.id')
                                        ->join('clothes_Specifications', 'clothes_orderinfo.speid', '=', 'clothes_Specifications.id')
                                        ->select('clothes_orderinfo.num', 'clothes_orderinfo.pay', 'clothes_shop.title', 'clothes_shop.pic', 'clothes_Specifications.colour', 'clothes_Specifications.size')
                                        ->get();

            $sum = 0;
            $total = 0;
            foreach ($order->orderInfos as $key => $info) {
                $picArr = explode(',', $info->pic);
                $info->pic = env('PIC_URL') . $picArr[0];
                $sum += $info->num;
                $total += $info->pay * $info->num;
            }
            $order->sum = $sum;
            $order->total = $total;
        }
        return json_encode($orders);
    }

    public function refund(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        $order->over = $request->over;
        $order->save();
        $refund = new OrderRefund;

        $refund->order_id = $request->order_id;
        $refund->member_id = $request->member_id;
        $refund->pic = $request->pic;
        $refund->content = $request->content;
        $refund->date = now();

        $data=$refund->save();

        if ($data == 1){
            return json_encode(['code' => '200', 'data' =>'申请成功']);
        } else{
            return json_encode(['code' => '400', 'data' =>'申请失败']);
        }
    }

    public function receipt($id)
    {
        $order = Order::findOrFail($id);

        $order->over = 3;
        $data = $order->save();

        if ($data == 1){
            return json_encode(['code' => '200', 'data' =>'成功']);
        } else{
            return json_encode(['code' => '400', 'data' =>'失败']);
        }
    }

    public function delete(Request $request)
    {
        Order::where('user', $request->user)->where('member', $request->member)->where('over', 0)->delete();
    }

    public function orderDelete($id)
    {
        $data = Order::destroy($id);

        if ($data == 1){
            return json_encode(['code' => '200', 'data' =>'删除成功']);
        } else{
            return json_encode(['code' => '400', 'data' =>'删除失败']);
        }
    }
}
