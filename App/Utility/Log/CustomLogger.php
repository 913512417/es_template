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
    function log(?string $msg, int $logLevel = self::LOG_LEVEL_INFO, string $category = 'DEBUG'): string
    {
        $date = udate();
        $filePath = $this->getPath($logLevel);
        $str = "---------------------------------------------------------------\n";
        $str .= "[{$date}] {$msg}\n";
        if(Core::getInstance()->runMode() == "dev"){
            echo $str;
        }
        file_put_contents($filePath,"{$str}",FILE_APPEND|LOCK_EX);
        return $str;
    }

    function console(?string $msg, int $logLevel = self::LOG_LEVEL_INFO, string $category = 'DEBUG')
    {
        $date = udate();
        $temp =  $this->colorString("[{$date}] : [{$msg}]",$logLevel)."\n";
        fwrite(STDOUT,$temp);
    }

    public function getPath(int $logLevel)
    {
        $dir = EASYSWOOLE_LOG_DIR."/".date('Y-m');
        switch($logLevel) {
            case self::LOG_LEVEL_NOTICE:
                $dir .= "/notice/";
                break;
            case self::LOG_LEVEL_WARNING:
                $dir .= "/warning/";
                break;
            case self::LOG_LEVEL_ERROR:
                $dir .= "/error/";
                break;
            default:
                $dir .= "/info/";
                break;
        }
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
            $old = umask(0);
            chmod($dir, 0777);
            umask($old);
        }
        $path = $dir . date('Y-m-d').".log";
        return $path;
    }

    private function colorString(string $str,int $logLevel)
    {
        switch($logLevel) {
            case self::LOG_LEVEL_NOTICE:
                $out = "[43m";
                break;
            case self::LOG_LEVEL_WARNING:
                $out = "[45m";
                break;
            case self::LOG_LEVEL_ERROR:
                $out = "[41m";
                break;
            default:
                $out = "[42m";
                break;
        }
        return chr(27) . "$out" . "{$str}" . chr(27) . "[0m";
    }

    private function levelMap(int $level)
    {
        switch ($level)
        {
            case self::LOG_LEVEL_NOTICE:
                return 'NOTICE';
            case self::LOG_LEVEL_WARNING:
                return 'WARNING';
            case self::LOG_LEVEL_ERROR:
                return 'ERROR';
            default:
                return 'INFO';
        }
    }
}