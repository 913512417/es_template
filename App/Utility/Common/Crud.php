<?php
/**
 * User: victor
 * Date: 2019/8/13
 * Time: 16:39
 * Description:怎删改查
 */
namespace App\Utility\Common;
use App\Define\ReturnCode;
trait Crud
{
    private $commonValiPath = "App\\Validate\\";
    private $commonModelPath = "App\\Model\\";
    public function commonAdd($name, $postData = []){
        if(!$postData){
            $postData = $this->input();
        }
        $validataName = $this->commonValiPath.$name;
        $modelName = $this->commonModelPath.$name;
        $validate = new $validataName();
        if(!$validate->scene('add')->check($postData)){
            return $this->returnJson(ReturnCode::FAILED, $validate->getError());
        }

        $returnStatus = $modelName::create()->data($validate->getData(), true)->save();
        if($returnStatus){
            return $this->returnJson(ReturnCode::SUCESS);
        }else{
            return $this->returnJson(ReturnCode::FAILED);
        }
    }

    public function commonUpdate($name, $postData = [],$where = [], $type = "edit", $callback=null){
        if(!$postData){
            $postData = $this->input();
        }
        $validataName = $this->commonValiPath.$name;
        $modelName = $this->commonModelPath.$name;
        $validate = new $validataName();
        if(!$validate->scene($type)->check($postData)){
            return $this->returnJson(ReturnCode::FAILED, $validate->getError());
        }

        if($where){
            $returnStatus = $modelName::create()->updateByWhere($where, $validate->getData());
        }else{
            $returnStatus = $modelName::create()->updateByPk($validate->getData());
        }

        if($returnStatus){
            if (is_callable($callback)) {
                call_user_func($callback);
            }
            return $this->returnJson(ReturnCode::SUCESS);
        }else{
            return $this->returnJson(ReturnCode::FAILED);
        }
    }

    public function commonDelete($name ,$where = [], $field= 'Fdelete_at'){
        $postData = $this->input();
        
        $validataName = $this->commonValiPath.$name;
        $modelName = $this->commonModelPath.$name;
        $validate = new $validataName();
        if(!$validate->scene('sigle')->check($postData)){
            return $this->returnJson(ReturnCode::FAILED, $validate->getError());
        }
        $data[$field] = time();
        if($where){
            $returnStatus = $modelName::create()->updateByWhere($where, $data);
        }else{
            $returnStatus = $modelName::create()->updateByPk($data);
        }
        
        if($returnStatus){
            return $this->returnJson(ReturnCode::SUCESS);
        }else{
            return $this->returnJson(ReturnCode::FAILED);
        }
    }

    public function commonAll($name ,$where = [], $fields = "*", $orderField = "", $orderAsc = "desc", $with=[]){
        $postData = $this->input();
        if(!isset($postData['page'])) $postData['page'] = 1;
        if(!isset($postData['limit'])) $postData['limit'] = 10;
        $modelName = $this->commonModelPath.$name;
        $model = $modelName::create()->field($fields);
        if ($with) {
            $model->with($with);
        }
        if($where){
            $model->where($where);
        }
        if($orderField){
            $model->order($orderField, $orderAsc);
        }
        $data = $model->pageinate($postData['page'], $postData['limit']);
        return $this->returnJson(ReturnCode::SUCESS, '', $data);

    }

}