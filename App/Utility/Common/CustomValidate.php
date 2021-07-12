<?php
/**
 * User: victor
 * Date: 2019/8/13
 * Time: 16:39
 * Description:验证类
 */
namespace App\Utility\Common;

class CustomValidate
{
    protected $rules = [];
    protected $messages = [];
    protected $currentScene = null;
    protected $scenes = [];
    protected $fields = [];
    protected $alias = [];
    protected $mapField = [];
    protected $error = "";
    protected $data = [];
    public function __construct(array $rules = [], array $messages = [], array $fields = [])
    {
        
        $this->rule    = $rules + $this->rules;
        $this->message = array_merge($this->messages, $messages);
        $this->field   = array_merge($this->fields, $fields);
    }
    public function mapField($postData){
        if($this->mapField){
            $data = [];
            foreach($postData as $key => $val){
                if(isset($this->mapField[$key])){
                    $data[$this->mapField[$key]] = $val;
                }
            }
            if(!$data) $data = $postData;
        }else{
            $data = $postData;
        }
        $this->data = $data;
        return $data;
    }
    /**
     * 验证参数
     */
    public function check($postData, $rules = [], $messages = []){
        if (empty($rules)) {
            $rules = $this->rules;
        }

        if (empty($messages)) {
            $messages = $this->messages;
        }

        $data = $this->mapField($postData);

        $alias = $this->alias;
        
        if($this->currentScene && isset($this->scenes[$this->currentScene])){
            $allowScene = $this->scenes[$this->currentScene];
            foreach($this->rules as $key => $val){
                if(in_array($key, $allowScene)){
                    $allowRules[$key] = $val;
                }
            }
            $rules = $allowRules;
        }
        if(!$rules){
            $this->error = '缺少必传参数';
            return false;
        }
        $validate = \EasySwoole\Validate\Validate::make($rules, $messages, $alias);
        $bool = $validate->validate($data);
        
        if($bool){
            return true;
        }else{
            $this->error = $validate->getError()->__toString();
            return false;
        }
    }

    /**
     * 设置验证场景
     */
    public function scene($name)
    {
        // 设置当前场景
        $this->currentScene = $name;

        return $this;
    }

    // 获取错误信息
    public function getError()
    {
        return $this->error;
    }

    public function getData(){
        return $this->data;
    }

}