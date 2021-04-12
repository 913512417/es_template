<?php
namespace App\HttpController;
use \EasySwoole\EasySwoole\Logger;
use EasySwoole\Log\LoggerInterface;
class Index extends BaseController
{

    public function index()
    {
        Logger::getInstance()->log('record level:DEBUG-category:debug log info',LoggerInterface::LOG_LEVEL_DEBUG,'debug');

        Logger::getInstance()->log('record level:INFO-category:info log info',LoggerInterface::LOG_LEVEL_INFO,'info');

        Logger::getInstance()->log('record level:NOTICE-category:notice log info',LoggerInterface::LOG_LEVEL_NOTICE,'notice');
    }

    function test()
    {

    }


}