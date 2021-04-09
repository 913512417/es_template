<?php
/**
 * User: victor
 * Date: 2019/11/21
 * Time: 11:15
 * Description:
 */

namespace App\Model;
use EasySwoole\ORM\AbstractModel;

class BaseModel extends AbstractModel
{

    /**
     * 分页
     * @param array $pageArg ['page'=>1,'limit'=>10]
     * @return array
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function pageinate(array $pageArg):array
    {
        $limit = $pageArg['limit']??10;
        $page = $pageArg['page']??1;
        $list = $this->limit($limit * ($page - 1), $limit)->withTotalCount()->select();
        $total = $this->lastQueryResult()->getTotalCount();
        return ['list'=>$list,'total_count'=>$total];
    }
    
}