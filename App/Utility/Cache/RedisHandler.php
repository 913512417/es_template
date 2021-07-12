<?php
namespace App\Utility\Cache;
use EasySwoole\Redis\Redis as RedisClient;
use EasySwoole\RedisPool\RedisPool;

class RedisHandler
{
    private  $redis;

    public function getRedis():?RedisClient
    {
        if(!$this->redis){
            $this->redis = RedisPool::defer();
        }
        return $this->redis;
    }


    public function get($key)
    {
        return RedisPool::invoke(function (RedisClient $redis) use ($key){
            return $redis->get($key);
        });
    }

    public function set($key, $data, $expTime = 0)
    {
        return RedisPool::invoke(function (RedisClient $redis) use ($key, $data, $expTime){
            $res = $redis->set($key,$data);
            if($res && $expTime > 0) {
                $redis->expire($key,$expTime);
            }
            return $res;
        });
    }

    public function del($key)
    {
        return RedisPool::invoke(function (RedisClient $redis) use ($key){
            if ($redis->exists($key)){
                return $redis->del($key);
            }else{
                return true;
            }
        });
    }

    public function exist($key)
    {
        return RedisPool::invoke(function (RedisClient $redis) use ($key){
            return $redis->exists($key);
        });
    }

    public function hSet($key,$data,$expTime = 0)
    {
        if (empty($data)){
            return false;
        }
        return RedisPool::invoke(function (RedisClient $redis) use ($key,$data,$expTime){
            $result = $redis->hMSet($key,$data);
            if ($result && $expTime > 0){
                $redis->expire($key,$expTime);
            }
            return $result;
        });
    }

    public function hGet($key,array $field = [])
    {
        return RedisPool::invoke(function (RedisClient $redis) use ($key,$field){
            if (!$redis->exists($key)){
                return [];
            }
            if (empty($field)){
                $data = $redis->hGetAll($key);
            }else{
                $data = $redis->hMGet($key,$field);
            }
            return $data;
        });
    }
}