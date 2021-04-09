<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/11/29
 * Time: 22:04
 */

namespace App\Utility\System;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Core;

class SqlEvent
{
    use Singleton;

    /**
     * @var \EasySwoole\ORM\Db\Result $ret
     */
    protected $ret = null;

    /**
     * @var \EasySwoole\Mysqli\QueryBuilder $temp
     */
    protected $temp = null;

    protected $start = null;


    /**
     * @param \EasySwoole\ORM\Db\Result $ret
     * @param \EasySwoole\Mysqli\QueryBuilder $temp
     * @param $start
     */
    public function registerEvent($ret, $temp, $start)
    {
        $this->ret = $ret;
        $this->temp = $temp;
        $this->start = $start;
        $this->catchSqlError();
        $this->getSql();
        $this->reset();
    }

    /**
     * 捕获sql错误
     * @throws \Exception
     */
    protected function catchSqlError(){
        $errCode = $this->ret->getLastErrorNo();
        if($errCode !== 0){
            $errMsg = $this->ret->getLastError();
            throw new \Exception($errMsg);
        }
    }

    protected function getSql(){
        if(Core::getInstance()->runMode() == "dev"){
            $sql = $this->temp->getLastQuery();
            echo $sql."\n";
        }
    }

    /**
     * 回收资源
     */
    private function reset()
    {
        $this->ret = null;
        $this->temp = null;
        $this->start = null;
    }
}