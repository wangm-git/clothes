<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test','TestController@index');

Route::get('/member/getInfo','MemberController@getInfo');

Route::get('/address/list/{member_id}','AddressController@list');
Route::post('/address/add','AddressController@add');
Route::post('/address/update/{id}','AddressController@update');
Route::post('/address/delete/{id}','AddressController@delete');
Route::post('/address/setDefault','AddressController@setDefault');

Route::get('/order/list','OrderController@list');

Route::get('/shop/show','ShopController@show');

Route::post('/cart/add','CartController@add');
Route::post('/cart/update','CartController@update');
Route::post('/cart/delete/{id}','CartController@delete');
Route::get('/cart/list','CartController@list');

Route::post('/pay/pay','PayController@pay');
Route::post('/pay/placeOrder','PayController@placeOrder');
Route::post('/pay/placeOrderFromCart','PayController@placeOrderFromCart');
Route::get('/pay/show','PayController@show');
