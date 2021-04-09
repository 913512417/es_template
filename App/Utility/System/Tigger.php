<?php


namespace App\Utility\System;


use EasySwoole\Log\LoggerInterface;
use EasySwoole\Trigger\Location;
use EasySwoole\Trigger\TriggerInterface;

class Tigger implements TriggerInterface
{
    protected $logger;

    function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function error($msg, int $errorCode = E_USER_ERROR, Location $location = null)
    {
        if($location != null){
            $msg .= "\n[ file ] {$location->getFile()} line:{$location->getLine()}";
        }
        $this->logger->log($msg,$this->errorMapLogLevel($errorCode));
    }

    public function throwable(\Throwable $throwable)
    {
        $msg = "{$throwable->getMessage()} \n[ file ]:{$throwable->getFile()} line:{$throwable->getLine()}";
        $this->logger->log($msg,LoggerInterface::LOG_LEVEL_ERROR);
    }

    private function errorMapLogLevel(int $errorCode)
    {
        switch ($errorCode){
            case E_PARSE:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                return LoggerInterface::LOG_LEVEL_ERROR;
            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
                return LoggerInterface::LOG_LEVEL_WARNING;
            case E_NOTICE:
            case E_USER_NOTICE:
                return LoggerInterface::LOG_LEVEL_NOTICE;
            case E_STRICT:
                return LoggerInterface::LOG_LEVEL_NOTICE;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return LoggerInterface::LOG_LEVEL_NOTICE;
            default :
                return LoggerInterface::LOG_LEVEL_INFO;
        }
    }
}