<?php


namespace EasySwoole\EasySwoole;


use App\Utility\Log\CustomLogger;
use App\Utility\System\ExceptionHandler;
use App\Utility\System\Middleware;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use App\Utility\Trigger\CustomTrigger;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        $customLogger = new CustomLogger();
        Di::getInstance()->set(SysConst::LOGGER_HANDLER, $customLogger);
        Di::getInstance()->set(SysConst::TRIGGER_HANDLER, new CustomTrigger($customLogger));
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER,[ExceptionHandler::class,'http']);
        //注册中间件
        Di::getInstance()->set('middleware',Middleware::class);
        //创建数据库连接
//        DbRegister::getInstance()->addDbConnection();

    }

    public static function mainServerCreate(EventRegister $register)
    {

    }
}