<?php
namespace App\Utility\Cache;

use App\Utility\Common\BaseModel;
use EasySwoole\Spl\SplArray;

abstract class BaseCache extends RedisHandler
{
    public static function create()
    {
        return new static();
    }


    /**
     * Notes: 多字段获取器
     * User: Victor
     * Date: 2021/5/10
     * Time: 11:57
     * @param BaseModel $model 指定数据模型
     * @param SplArray $data 数据
     * @param array $field 那些字段需要获取器 不传则所有字段匹配获取器
     */
    public function getAttrs(BaseModel $model,SplArray $data,$fields = [])
    {
        if(!empty($fields)){
            foreach ($fields as $field){
                $data[$field] = $this->getAttr($field,$model,$data);
            }
        }else{
            foreach ($data as $field => $item){
                $data[$field] = $this->getAttr($field,$model,$data);
            }
        }
    }

    /**
     * Notes: 单字段获取器
     * User: Victor
     * Date: 2021/5/10
     * Time: 11:57
     * @param string $field 字段
     * @param BaseModel $model 指定数据模型
     * @param SplArray $data 数据
     */
    public function getAttr(string $field,BaseModel $model,SplArray $data)
    {
        $method = 'get' . str_replace( ' ', '', ucwords( str_replace( ['-', '_'], ' ', $field ) ) ) . 'Attr';
        if (method_exists($model, $method)) {
            return call_user_func([$model,$method],$data[$field] ?? null, $data);
        }
        return null;
    }
}