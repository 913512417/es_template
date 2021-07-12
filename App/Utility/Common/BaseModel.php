<?php
/**
 * User: victor
 * Date: 2019/11/21
 * Time: 11:15
 * Description:
 */

namespace App\Utility\Common;
use EasySwoole\ORM\AbstractModel;

abstract class BaseModel extends AbstractModel
{
    // 设置当前模型的数据库连接
    protected $connectionName = 'default';

    /**
     * 通过条件更新数据
     * @param array $where
     * @param array $data
     * @return bool
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function updateByWhere(array $where,array $data)
    {
        $res = $this->update($data, $where);
        if(!$res) return false;
        $count = $this->lastQueryResult()->getAffectedRows();
        return $count?true:false;
    }

    /**
     * 通过主键更新数据
     * @param $pk
     * @param array $data
     * @return bool
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function updateByPk(array $data, $pk = "")
    {
        if($pk){
            $res = $this->update($data, [$this->pk, $pk]);
        }else{
            $res = $this->update($data, [$this->pk, $data[$this->pk]]);
        }
        
        if(!$res) return false;
        $count = $this->lastQueryResult()->getAffectedRows();
        return $count?true:false;
    }


    /**
     * 获取分页数据
     * @param $page
     * @param $limit
     * @return bool
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function pageinate($page = 1, $limit = 10)
    {
        $model = $this->limit($limit * ($page - 1), $limit)->withTotalCount();
        // 列表数据
        $list = $model->all(null);
        $result = $model->lastQueryResult();
        // 总条数
        $total = $result->getTotalCount();
        return ['per_page'=>$limit, 'total'=>$total, 'data'=>$list, 'last_page'=>ceil($total/$limit), 'current_page'=>$page];
    }
}