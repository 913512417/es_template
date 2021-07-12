<?php
/**
 * User: victor
 * Date: 2020/1/6
 * Time: 16:18
 * Description:
 */
namespace App\Utility\System;

use EasySwoole\Http\Request;

class Middleware
{
    protected $default_namespace = 'App\\Middleware\\';

    protected $queue = [];

    public function __construct($default_namespace = '')
    {
        if($default_namespace) $this->default_namespace = $default_namespace;
    }

    public function dispath(Request $request,$rule,$actionName)
    {
        try {
            foreach ($rule as $key => $value){
                if(!is_numeric($key)){
                    $middleware = ucfirst($key);
                    if(!$this->isVerify($actionName,$value)) continue;
                }else{
                    $middleware = ucfirst($value);
                }
                $class = $this->buildMiddleware($middleware);
                $request->actionName = $actionName;
                $res = call_user_func([$class,'handle'],$request);
                if($res !== true) return $res;
            }
            return true;
        }catch (\Throwable $exception){
            throw $exception;
        }
    }

    /**
     * 解析中间件
     * @param $middlewareName
     * @return mixed
     * @throws \Exception
     */
    protected function buildMiddleware($middlewareName)
    {
         if(!isset($this->queue[$middlewareName])){
             $class = $this->default_namespace.$middlewareName;
             if(class_exists($class)){
                 $this->queue[$middlewareName] = new $class;
             }else {
                 throw new \Exception('no middleware class match');
             }
         }
        return $this->queue[$middlewareName];
    }

    /**
     * 验证是否需要执行中间件
     * @param $actionName
     * @param $actions
     * @return bool
     */
    protected function isVerify($actionName,$actions)
    {
        $exceptActions = isset($actions['except']) ? $actions['except'] : [];
        $onlyActions = isset($actions['only']) ? $actions['only'] : [];
        if($onlyActions){
            if(is_string($onlyActions)) $onlyActions = [$onlyActions];
            return in_array($actionName,$onlyActions)?true:false;
        }else if($exceptActions){
            if(is_string($exceptActions)) $exceptActions = [$exceptActions];
            return in_array($actionName,$exceptActions)?false:true;
        }else{
            if(is_string($actions)) $actions = [$actions];
            return in_array($actionName,$actions)?true:false;
        }
    }


}