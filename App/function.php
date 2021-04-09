<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/1
 * Time: 20:52
 * intro:助手函数
 */

//生成毫秒时间戳
function msection(){
    list($msec,$sec) = explode(' ',microtime());
    return $sec*1000+intval($msec*1000);
}

function udate() {
    $time = time();
    $format = date("Y-m-d H:i:s",$time);
    $milliseconds = msection() - $time*1000;
    return $format.".".$milliseconds;
}