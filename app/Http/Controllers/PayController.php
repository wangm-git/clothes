<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// use Yansongda\Pay\Pay;
use App\Order;
use Pay;

class PayController extends Controller
{
    // public function index(Request $request)
    // {
    // 	$address = Address::find($request->address_id)

    // 	$code = $this->createCode();

    // 	foreach ($request->shop_ids as $key => $shop) {
    // 		# code...
    // 	}


    // }

    public function createCode()
    {
    	$code = date('Ymdhis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

    	$order = Order::where('order_no', $code)->first();
    	if ($order->isEmpty()) {
    		return $code;
    	} else {
    		$this->createCode();
    	}
    	
    }

    public function pay()
    {
		 // $config_biz = [
   //          'out_trade_no' => 'e2',
   //          'total_fee' => '0.01',
   //          'body' => 'test body',
   //          'spbill_create_ip' => '115.28.129.234',
   //          'openid' => 'o8qsZ4-FNUkKZ_nf3ACkgGhOnjwk',
   //      ];

   //      $pay = new Pay($this->config);

   //      return $pay->driver('wechat')->gateway('mp')->pay($config_biz);
    	$order = [
		    'out_trade_no' => time(),
		    'body' => 'subject-测试',
		    'total_fee' => '1',
		    'openid' => 'ocBUw5V8IixQYG9MratbllY1wyEU',
		];

		$result = Pay::wechat()->miniapp($order);
		return $result;
    }

    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！

            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// laravel 框架中请直接 `return $pay->success()`
    }
}
