<?php
namespace App\Model;


class SystemConf extends BaseModel
{
    protected $tableName = 'v_system_conf';

    const VALUE_TYPE_JSON = "json";
    const VALUE_TYPE_TEXT = 'text';

    public function getConf($key)
    {

    }
}