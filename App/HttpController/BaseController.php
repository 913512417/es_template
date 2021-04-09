<?php


namespace App\HttpController;

use App\Utility\Common\ReturnMsg;

use App\Utility\Log\CustomLogger;
use App\Utility\Log\RequestLog;
use EasySwoole\Component\Di;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class BaseController extends Controller
{
    protected $middlewareRule = [];

    /** @var RequestLog */
    private $requestLog = null;

    //=================系统方法======================================

    protected function onRequest(?string $action): ?bool
    {
        return $this->middleware();
    }

    protected function afterAction(?string $actionName): void
    {
        parent::afterAction($actionName); // TODO: Change the autogenerated stub
        if ($this->requestLog){
            $this->requestLog->httpServer($this->request(),$this->response())->writeLog();
        }
    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if(!is_file($file)){
            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }

    protected function gc()
    {
        parent::gc(); // TODO: Change the autogenerated stub
        $this->requestLog = null;
    }

    protected function middleware()
    {
        //中间件
        $res = Di::getInstance()->get('middleware')->dispath($this->request(), $this->middlewareRule, $this->getActionName());
        if($res !== true){
            $this->returnJson($res);
            return false;
        }else{
            return true;
        }
    }

    //=================自定义方法======================================
    /**
     * Notes: 返回json格式数据
     * User: Victor
     * Date: 2021/2/21
     * Time: 17:18
     * @param $returnCode
     * @param string $msg
     * @param null $data
     * @return bool
     */
    protected function returnJson($returnCode,$msg = "",$data = null)
    {
        if (!$this->response()->isEndResponse()) {
            //兼容两种返回模式
            if(is_array($returnCode)){
                if(!isset($returnCode['msg'])) $returnCode['msg'] = ReturnMsg::RERTURN_MSG[$returnCode['errcode']] ?? "";
                $result = $returnCode;
            }else{
                $result = [
                    "errcode" => $returnCode,
                    "data" => $data ,
                    "msg" => $msg?$msg:(ReturnMsg::RERTURN_MSG[$returnCode]??"")
                ];
            }
            $this->response()->write(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus(Status::CODE_OK);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Notes: 返回图片格式
     * User: Victor
     * Date: 2021/2/21
     * Time: 17:19
     * @param $returnCode
     * @return bool
     */
    protected function returnImg($data)
    {
        if (!$this->response()->isEndResponse()) {
            $this->response()->write($data);
            $this->response()->withHeader('Content-type', 'image/jpg');
            $this->response()->withStatus(Status::CODE_OK);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取get/post参数
     * @param null $name
     * @param null $default
     * @return array|mixed|null
     */
    protected function input($name = null, $default = null)
    {
        if($name){
            $value = $this->request()->getRequestParam($name);
            if(!$value){
                $content = $this->request()->getBody()->__toString();
                if($content){
                    $raw_array = json_decode($content, true);
                    $value = isset($raw_array[$name]) ? $raw_array[$name] : '';
                }
            }
            return $value ? $value : $default;
        }else{
            $raw_array = $this->request()->getRequestParam();
            if(!$raw_array){
                $content = $this->request()->getBody()->__toString();
                if($content){
                    $raw_array = json_decode($content, true);
                }
            }
            return $raw_array;
        }
    }

    /**
     * Notes: 获取分页参数
     * User: Victor
     * Date: 2021/2/21
     * Time: 17:21
     * @return array
     */
    protected function pageArg()
    {
        $res = [];
        $res["limit"] = $this->input("limit",10);
        $res["page"] = $this->input("page",1);
        return $res;
    }

    /**
     * Notes: 写入日志
     * User: Victor
     * Date: 2021/3/5
     * Time: 14:23
     * @param int $logLevel
     * @param RequestLog|null $requestLog 不传则用系统默认日志数据
     */
    protected function writeRequestLog($logLevel = CustomLogger::LOG_LEVEL_INFO,RequestLog $requestLog = null)
    {
        if ($requestLog){
            $this->requestLog = $requestLog->setLogLevel($logLevel);
        }else{
            $this->requestLog = RequestLog::create()->setLogLevel($logLevel);
        }
    }
}