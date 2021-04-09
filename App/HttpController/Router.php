<?php
/**
 * User: victor
 * Date: 2019/11/19
 * Time: 11:38
 * Description:
 */

namespace App\HttpController;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    public function initialize(RouteCollector $route)
    {
        $this->setGlobalMode(true);
    }

}
