<?php
/**
 * User: victor
 * Date: 2020/2/12
 * Time: 11:08
 * Description:
 */
namespace App\Utility\System;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;
use EasySwoole\ORM\Db\Config as DbConfig;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;

class DbRegister
{
    use Singleton;

    /**
     * 获取数据库配置
     * @return array|mixed|null
     */
    protected function getDbConf()
    {
        $file = Config::getInstance()->getConf('MYSQL_CONF');
        if(file_exists($file)){
            $data = require $file;
            return $data;
        }else{
            throw new \Exception("db config file : {$file} is miss");
        }
    }

    /**
     * 添加数据库连接
     */
    public function addDbConnection()
    {
        foreach ($this->getDbConf() as $dbName => $conf)
        {
            $hostnames = $conf['hostname'];
            if(is_array($hostnames)){
                $dbNum = count($hostnames);
                for($i = 0; $i < $dbNum; $i++)
                {
                    $data = [
                        'hostname' => $conf['hostname'][$i],
                        'database' => $conf['database'],
                        'username' => is_array($conf['username'])?$conf['username'][$i]:$conf['username'],
                        'password' => is_array($conf['password'])?$conf['password'][$i]:$conf['password'],
                        'charset' => $conf['charset'],
                        'port' => is_array($conf['port'])?$conf['port'][$i]:$conf['port'],
                    ];
                    $dbConf = $this->setDbConf($data,$conf['poolConf']);
                    DbManager::getInstance()->addConnection(new Connection($dbConf),$this->getConnectionName($i,$dbName));
                }
            }else{
                $dbConf = $this->setDbConf($conf,$conf['poolConf']);
                DbManager::getInstance()->addConnection(new Connection($dbConf),$this->getConnectionName(0,$dbName));
            }

        }
        $this->addDbEvent();
    }

    /**
     * 设置数据库配置
     * @param $conf 数据库基础配置
     * @param $poolConf 连接池配置
     * @return DbConfig
     * @throws \EasySwoole\Pool\Exception\Exception
     */
    protected function setDbConf($conf,$poolConf)
    {
        $dbConf = new DbConfig();
        $dbConf->setHost($conf['hostname']);
        $dbConf->setDatabase($conf['database']);
        $dbConf->setUser($conf['username']);
        $dbConf->setPassword($conf['password']);
        $dbConf->setCharset($conf['charset']);
        $dbConf->setPort($conf['port']);

        $dbConf->setGetObjectTimeout($poolConf['getObjectTimeout']); //设置获取连接池对象超时时间
        $dbConf->setIntervalCheckTime($poolConf['intervalCheckTime']); //设置检测连接存活执行回收和创建的周期
        $dbConf->setMaxIdleTime($poolConf['maxIdleTime']); //连接池对象最大闲置时间(秒)
        $dbConf->setMaxObjectNum($poolConf['maxObjectNum']); //设置最大连接池存在连接对象数量
        $dbConf->setMinObjectNum($poolConf['minObjectNum']); //设置最小连接池存在连接对象数量
        $dbConf->setAutoPing($poolConf['autoPing']);
        return $dbConf;
    }

    /**
     * 获取连接名
     * 第一个为写入库  后面的为读库
     * 读库连接名 dbName_"read_1"
     * @param $seq 服务器序号 从0 开始
     * @param $dbName 写库连接名
     * @return string
     */
    protected function getConnectionName($seq,$dbName)
    {
        if($seq === 0)
        {
            return $dbName;
        }
        return $dbName.'_read_'.$seq;
    }

    /**
     * 添加db事件
     */
    protected function addDbEvent()
    {
        //注册sql执行后的回调事件
        DbManager::getInstance()->onQuery(function ($ret, $temp, $start)
        {
            SqlEvent::getInstance()->registerEvent($ret,$temp,$start);
        });
    }


}