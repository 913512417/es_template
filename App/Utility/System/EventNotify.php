<?php
/**
 * User: victor
 * Date: 2020/3/12
 * Time: 11:56
 * Description:
 */

namespace App\Utility\System;

use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\Trigger;
use Swoole\Table;
use EasySwoole\Component\Singleton;

class EventNotify
{
    use Singleton;

    private $evenTable;

    function __construct()
    {
        $this->evenTable = new Table(2048);
        $this->evenTable->column('expire',Table::TYPE_INT,8);
        $this->evenTable->column('count',Table::TYPE_INT,8);
        $this->evenTable->create();
    }

    function notifyException(\Throwable $throwable)
    {
        $class = get_class($throwable);
        //根目录下的异常，以msg为key
        if($class == 'Exception'){
            $key = substr(md5($throwable->getMessage()),8,16);
        }else{
            $key = substr(md5($class),8,16);
        }
        $this->onNotify($key,$throwable->getMessage());
    }

    function notify(string $msg)
    {
        $key = md5($msg);
        $this->onNotify($key,$msg);
    }

    private function onNotify(string $key,string $msg)
    {
        if(Core::getInstance()->isDev()) return;
        $info = $this->evenTable->get($key);
        //同一种消息在十分钟内不再记录
        $this->evenTable->set($key,["expire"=>time() + 10 * 60]);
        if(!empty($info)) return;
        try{
            //todo 实现通知开发者
            preg_match("/\[ INFO ] (.*?)\n\[ DATA ]/i",$msg,$match);
            if($match){
                $msg = $match[1];
            }else{
                $msg = "[".date('H:i:s')."]系统错误!";
            }
//            systemErrorNotice(config('mobile_msg.msg_prefix').$msg);
        }catch (\Throwable $throwable){
            //避免死循环
            Trigger::getInstance()->error($throwable->getMessage());
        }
    }
}