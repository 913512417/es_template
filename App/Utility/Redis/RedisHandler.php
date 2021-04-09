<?php
namespace App\Utility\Redis;

use EasySwoole\Redis\Redis as RedisClient;
use EasySwoole\RedisPool\Redis as RedisPool;
class RedisHandler
{
    private  $redis;
    public static function create()
    {
        return new static();
    }

    public function getRedis():?RedisClient
    {
        if(!$this->redis){
            $this->redis = RedisPool::defer('redis');
        }
        return $this->redis;
    }

    public function get($key)
    {
        return RedisPool::invoke('redis',function (RedisClient $redis) use ($key){
             return $redis->get($key);
        });
    }

    public function set($key, $data, $expTime = 0)
    {
        $param = [
          'key' => $key,
          'data' => $data,
          'expTime' => $expTime
        ];
        return RedisPool::invoke('redis',function (RedisClient $redis) use ($param){
            $res = $redis->set($param['key'],$param['data']);
            if($res && $param['expTime'] > 0) {
                $redis->expire($param['key'],$param['expTime']);
            }
            return $res;
        });
    }

    public function del($key)
    {
        return RedisPool::invoke('redis',function (RedisClient $redis) use ($key){
            return $redis->del($key);
        });
    }


    public function pushList($key,$data,$direction = 'left')
    {
        return RedisPool::invoke('redis',function (RedisClient $redis) use ($key,$data,$direction){
            return $direction === 'left'?$redis->lPush($key,$data):$redis->rPush($key,$data);
        });
    }

    /**
     * Notes: 获取列表数据
     * User: Victor
     * Date: 2020/8/28
     * Time: 15:05
     * @param $key
     * @param string $direction
     */
    public function popList($key,$direction = 'rigth')
    {
        return RedisPool::invoke('redis',function (RedisClient $redis) use ($key,$direction){
            return $direction === 'left'?$redis->lPop($key):$redis->rPop($key);
        });
    }

    public function listLen($key){
        return RedisPool::invoke('redis',function (RedisClient $redis) use ($key){
            return $redis->lLen($key);
        });
    }

    public function sAdd($key,$data)
    {
        return RedisPool::invoke('redis',function (RedisClient $redis) use ($key,$data){
            return $redis->sAdd($key,$data);
        });
    }


}