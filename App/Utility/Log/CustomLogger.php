<?php
/**
 * Created by PhpStorm.
 * User: Yimir
 * Date: 2020/02/28
 * Time: 13:47
 */
namespace App\Utility\Log;

use EasySwoole\EasySwoole\Core;
use EasySwoole\Log\LoggerInterface;

class CustomLogger implements LoggerInterface
{
    const LOG_LEVEL_QEQUEST = 5;

    function log(?string $msg, int $logLevel = self::LOG_LEVEL_INFO, string $category = 'DEBUG'): string
    {
        $date = udate();
        $levelStr = $this->levelMap($logLevel);
        $filePath = $this->getPath();
        $str = "---------------------------------------------------------------\n";
        $str .= "[{$date}][{$levelStr}]:{$msg}\n";
        file_put_contents($filePath,"{$str}",FILE_APPEND|LOCK_EX);
        return $str;
    }

    function console(?string $msg, int $logLevel = self::LOG_LEVEL_INFO, string $category = 'DEBUG')
    {
        $date = udate();
        $levelStr = $this->levelMap($logLevel);
        echo "[{$date}][{$levelStr}]:{$msg}\n";
    }

    public function getPath()
    {
        $dir = EASYSWOOLE_LOG_DIR."/".date('Y-m')."/";
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
            $old = umask(0);
            chmod($dir, 0777);
            umask($old);
        }
        $path = $dir . date('Ymd').".log";
        return $path;
    }

    private function levelMap(int $level)
    {
        switch ($level)
        {
            case self::LOG_LEVEL_DEBUG:
                return 'debug';
            case self::LOG_LEVEL_INFO:
                return 'info';
            case self::LOG_LEVEL_NOTICE:
                return 'notice';
            case self::LOG_LEVEL_WARNING:
                return 'warning';
            case self::LOG_LEVEL_ERROR:
                return 'error';
            case self::LOG_LEVEL_QEQUEST:
                return 'request';
            default:
                return 'unknown';
        }
    }
}