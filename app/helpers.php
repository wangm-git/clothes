<?php
/**
 * Created by shmilyelva
 * Date: 2019/3/26
 * Time: 下午4:28
 */
if (!function_exists('p')) {
    // 传递数据以易于阅读的样式格式化后输出
    function p($data)
    {
        $array = [];
        // 定义样式
        foreach($data as $key=>$value) {
            $array[$key] = json_decode(json_encode($value), true);
        }
        print_r($array);
    }
}

if (!function_exists('debug')) {
    // 传递数据以易于阅读的样式格式化后输出并终止
    function debug($data)
    {
        p($data);
        die;
    }
}
