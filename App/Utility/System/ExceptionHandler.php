<?php
/**
 * User: victor
 * Date: 2020/1/7
 * Time: 11:51
 * Description:
 */

namespace App\Utility\System;
use App\Utility\Log\CustomLogger;
use App\Utility\Log\RequestLog;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class ExceptionHandler
{
    public static function http( \Throwable $exception, Request $request, Response $response )
    {
        $response->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->write(nl2br('系统错误！'));
        RequestLog::create()->httpServer($request,$response)
            ->setLogLevel(CustomLogger::LOG_LEVEL_ERROR)
            ->setLocation(getLocation($exception))
            ->writeLog();
    }
}