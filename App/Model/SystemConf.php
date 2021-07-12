<?php
namespace App\Model;


use App\Utility\Common\BaseModel;

class SystemConf extends BaseModel
{
    protected $tableName = 'v_system_conf';

    const DATA_FORMT_TEXT = 0;
    const DATA_FORMT_JSON = 1;
    const DATA_FORMT_XML = 2;
    const DATA_FPRMT_SERIALIZE = 3;

    public function getConf($key)
    {
        if ($key == ""){
            return "";
        }
        $temp = explode(".",$key,5);
        $topKey = array_shift($temp);
        $data = $this->where("conf_alias",$topKey)->field("value,value_type")->get();
        if (!$data){
            return "";
        }
        $confs = $this->dataToArray($data->value,$data->value_type);
        $count = count($temp);
        if ($count > 0){
            for ($i = 0; $i < $count; $i++){
                $key = $temp[$i];
                if (isset($confs[$key])){
                    $confs = $confs[$key];
                }else{
                    return "";
                }
            }
        }
        return $confs;
    }

    private function dataToArray($data,$dataForamt)
    {
        switch (intval($dataForamt)){
            case self::DATA_FORMT_JSON:
                return json_decode($data,true);
            case self::DATA_FORMT_XML:
                return xmlToArray($data);
            case self::DATA_FPRMT_SERIALIZE:
                return unserialize($data);
            default:
                return $data;
        }
    }
}