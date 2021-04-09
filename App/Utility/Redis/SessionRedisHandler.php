<?php
namespace App\Utility\Redis;

class SessionRedisHandler implements \SessionHandlerInterface
{

    private $expTime = 7200; // 默认超时时间 根据业务场景设置

    function __construct(){

    }

    function open($path, $name)
    {
        return true;
    }

    function close(){
        return true;
    }

    function read($session_id)
    {
        $value = RedisHandler::create()->get("baiqu_".$session_id);
        if ($value){
            return $value;
        }
        return '';
    }

    function write($session_id, $data)
    {

        return RedisHandler::create()->set("baiqu_".$session_id, $data,$this->expTime);
    }

    function destroy($session_id)
    {
        return RedisHandler::create()->del("baiqu_".$session_id);
    }

    function gc($maxlifetime)
    {
        return true; // 因为redis设置了过期时间，不需要再gc回收
    }

    function __destruct()
    {
        session_write_close();
    }
}