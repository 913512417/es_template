<?php
/**
 * User: victor
 * Date: 2020/3/12
 * Time: 11:56
 * Description:
 */

namespace App\Utility\System;

use EasySwoole\Component\Di;
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
        $info = $this->evenTable->get($key);
        //同一种消息在十分钟内不再记录
        $this->evenTable->set($key,["expire"=>time() + 10 * 60]);
        if(!empty($info)) return;
        try{
            $obj = Di::getInstance()->get('errorNotify');
            if ($obj){
                call_user_func($obj,$msg);
            }
        }catch (\Throwable $throwable){
            //避免死循环
            Trigger::getInstance()->error($throwable->getMessage());
        }
    }
}