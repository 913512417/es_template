<?php
/**
 * User: victor
 * Date: 2020/1/7
 * Time: 11:51
 * Description:
 */

namespace App\Utility\System;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Trigger\Location;

class ExceptionHandler
{
    public static function http( \Throwable $exception, Request $request, Response $response )
    {
        $response->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->write(nl2br('系统错误！'));
        $msg = getRequestLog($request);
        $msg.= "[ ERROR ] ".$exception->getMessage();
        $location = new Location();
        $location->setFile($exception->getFile());
        $location->setLine($exception->getLine());
        Trigger::getInstance()->error($msg, E_USER_ERROR,$location);
    }
}