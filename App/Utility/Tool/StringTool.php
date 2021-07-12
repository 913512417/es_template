<?php


namespace App\Utility\Tool;


class StringTool
{
    //随机字符串
    public static function randomStr($len)
    {
        $strs="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        return substr(str_shuffle($strs),0,$len);
    }

    function xmlToArray($xml){
        $obj = simplexml_load_string($xml,"SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_decode(json_encode($obj),true);
        return $json;
    }
}