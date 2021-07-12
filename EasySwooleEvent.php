<?php


namespace EasySwoole\EasySwoole;


use App\Utility\Log\CustomLogger;
use App\Utility\System\EventNotify;
use App\Utility\System\ExceptionHandler;
use App\Utility\System\HotReload;
use App\Utility\System\Middleware;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use App\Utility\Trigger\CustomTrigger;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        $customLogger = new CustomLogger();
//        Di::getInstance()->set('errorNotify',[Common::class,'errorNotify']);
        Di::getInstance()->set(SysConst::LOGGER_HANDLER, $customLogger);
        Di::getInstance()->set(SysConst::TRIGGER_HANDLER, new CustomTrigger($customLogger));
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER,[ExceptionHandler::class,'http']);
        //注册中间件
        Di::getInstance()->set('middleware',Middleware::class);
        //创建数据库连接
//        DbRegister::getInstance()->addDbConnection();
        self::iniRedis();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        if (Core::getInstance()->runMode() == 'dev'){
            $swooleServer = ServerManager::getInstance()->getSwooleServer();
            $swooleServer->addProcess((new HotReload('HotReload', ['disableInotify' => true]))->getProcess());
        }
        Trigger::getInstance()->onException()->set('notify',function (\Throwable $throwable){
            EventNotify::getInstance()->notifyException($throwable);
        });
        Trigger::getInstance()->onError()->set('notify',function ($msg){
            EventNotify::getInstance()->notify($msg);
        });
    }

    private static function iniRedis()
    {
        $redisPoolConfig = RedisPool::getInstance()->register(new RedisConfig(\EasySwoole\EasySwoole\Config::getInstance()->getConf("REDIS")));
        //配置连接池连接数
        $redisPoolConfig->setMinObjectNum(5);
        $redisPoolConfig->setMaxObjectNum(20);
    }
}