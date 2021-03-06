<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/1
 * Time: 20:52
 * intro:助手函数
 */
use EasySwoole\Http\Request;
use \EasySwoole\Log\LoggerInterface;
use EasySwoole\Trigger\Location;

//生成毫秒时间戳
function msection(){
    list($msec,$sec) = explode(' ',microtime());
    return $sec*1000+intval($msec*1000);
}

function udate() {
    $time = time();
    $format = date("Y-m-d H:i:s",$time);
    $milliseconds = msection() - $time*1000;
    return $format.".".$milliseconds;
}

/**
 * 自定义日志函数（如果添加新日志文件，需要配置LOG文件）
 * @param $msg 类型 字符串或数组
 * @param string $level info debug ....
 * @param string $data 数据
 */
function writeLog(string $msg,$level = LoggerInterface::LOG_LEVEL_INFO, $data = null ,Location $location = null)
{
    $str = $msg;
    if($data){
        $str .= "\n[ CUSTOM_DATA ] ";
        if(is_array($data) || is_object($data)){
            $str .= var_export($data,true);
        }else{
            $str .= $data;
        }
    }
    if ($level == LoggerInterface::LOG_LEVEL_ERROR){
        $error = error_get_last();
        if (!$error){
            $error = E_USER_ERROR;
        }
        \EasySwoole\EasySwoole\Trigger::getInstance()->error($str,$error,$location);
    }else{
        \EasySwoole\EasySwoole\Logger::getInstance()->log($str, $level, 'INFO');
    }

}

/**
 * Notes:
 * User: Victor
 * Date: 2021/4/12
 * Time: 10:53
 * $throwable Throwable
 * @return Location
 */
function getLocation(Throwable $throwable = null):Location
{
    $location = new Location();
    if(!$throwable){
        $debugTrace = debug_backtrace();
        $caller = array_shift($debugTrace);
        $location->setLine($caller['line']);
        $location->setFile($caller['file']);
    }else{
        $location->setFile($throwable->getFile());
        $location->setLine($throwable->getLine());
    }
    return $location;
}

function getRequestData(Request $request,$name,$default = "")
{
    if($name){
        $value = $request->getRequestParam($name);
        if(!$value){
            $content = $request->getBody()->__toString();
            if($content){
                $raw_array = json_decode($content, true);
                $value = isset($raw_array[$name]) ? $raw_array[$name] : '';
            }
        }
        return $value ? $value : $default;
    }else{
        $raw_array = $request->getRequestParam();
        if(!$raw_array){
            $content = $request->getBody()->__toString();
            if($content){
                $raw_array = json_decode($content, true);
            }
        }
        return $raw_array;
    }
}