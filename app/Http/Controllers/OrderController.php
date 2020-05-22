<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderInfo;

class OrderController extends Controller
{
    public function list(Request $request)
    {
        $orders = Order::where('user', $request->user_id)
                        ->where('over', $request->over)
                        ->orderBy('id', 'asc')
                        ->select('id', 'over')
                        ->skip($request->skip)
                        ->take(1)
                        ->get();

        foreach ($orders as $key => $order) {
            $order->orderInfos = OrderInfo::where('oid', $order->id)
                                        ->join('clothes_shop', 'clothes_orderinfo.shopid', '=', 'clothes_shop.id')
                                        ->join('clothes_specifications', 'clothes_orderinfo.speid', '=', 'clothes_specifications.id')
                                        ->select('clothes_orderinfo.num', 'clothes_orderinfo.pay', 'clothes_shop.title', 'clothes_shop.pic', 'clothes_specifications.colour', 'clothes_specifications.size')
                                        ->get();

            $sum = 0;
            $total = 0;
            foreach ($order->orderInfos as $key => $info) {
                $picArr = explode(',', $info->pic);
                $info->pic = env('APP_URL') . $picArr[0];
                $sum += $info->num;
                $total += $info->pay * $info->num;
            }
            $order->sum = $sum;
            $order->total = $total;
        }
        echo $orders;
    }
}
