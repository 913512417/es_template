<?php
namespace App\Utility\Common;
use App\Utility\Log\CustomLogger;
use App\Utility\Log\RequestLog;
use EasySwoole\HttpClient\Bean\Response;
use EasySwoole\HttpClient\HttpClient;
use EasySwoole\Spl\SplArray;

class HttpRequest
{
    /** @var HttpClient */
    protected $http;
    /** @var Response */
    protected $response;
    /** @var string */
    protected $url;
    /** @var array */
    protected $postData = [];
    /** @var string */
    protected $method = "GET";
    /** @var bool  */
    protected $autoWriteLog = true;
    /** @var bool  */
    protected $enableSSl = false;


    public function __construct(?string $url)
    {
        $this->url = $url;
        $this->setHttp(new HttpClient($url));
        $this->setTimeout(10.0);
        $this->setConnectTimeout(30.0);
    }
    
    public static function create(string $url = null)
    {
        return new static($url);
    }

    /**
     * @return HttpClient
     */
    public function getHttp(): HttpClient
    {
        return $this->http;
    }

    /**
     * @param HttpClient $http
     * @return HttpRequest
     */
    protected function setHttp(HttpClient $http): HttpRequest
    {
        $this->http = $http;
        return $this;
    }


    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return HttpRequest
     */
    protected function setResponse(Response $response): HttpRequest
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnableSSl(): bool
    {
        return $this->enableSSl;
    }

    /**
     * @param bool $enableSSl
     * @return HttpRequest
     */
    public function setEnableSSl(bool $enableSSl): HttpRequest
    {
        $this->http->setEnableSSL($enableSSl);
        $this->enableSSl = $enableSSl;
        return $this;
    }

    /**
     * @return array
     */
    public function getPostData(): array
    {
        return $this->postData;
    }

    /**
     * @param array $postData
     * @return HttpRequest
     */
    public function setPostData(array $postData): HttpRequest
    {
        $this->postData = $postData;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return HttpRequest
     */
    public function setMethod(string $method = "GET"): HttpRequest
    {
        $this->method = strtolower($method);
        return $this;
    }

    /**
     * @param array $header
     * @param bool $isMerge
     * @param bool $strtolower
     * @return HttpRequest
     */
    public function setHeader(array $header,$isMerge = true, $strtolower = true): HttpRequest
    {
        $this->http->setHeaders($header,$isMerge,$strtolower);
        return $this;
    }

    /**
     * @param float $timeout
     * @return HttpRequest
     */
    public function setTimeout(float $timeout): HttpRequest
    {
        $this->http->setTimeout($timeout);
        return $this;
    }

    /**
     * @param float $connectTimeout
     * @return HttpRequest
     */
    public function setConnectTimeout(float $connectTimeout): HttpRequest
    {
        $this->http->setConnectTimeout($connectTimeout);
        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoWriteLog(): bool
    {
        return $this->autoWriteLog;
    }

    /**
     * 开启自动写入日志
     * @param bool $autoWriteLog
     * @return HttpRequest
     */
    public function setAutoWriteLog(bool $autoWriteLog): HttpRequest
    {
        $this->autoWriteLog = $autoWriteLog;
        return $this;
    }

    /**
     * Notes: 发送请求
     * User: Victor
     * Date: 2021/3/2
     * Time: 11:31
     * @param $bodyFormat $response body 返回结果格式 default json
     * @return mixed
     */
    public function send($bodyFormat = "json"):mixed
    {
        switch ($this->getMethod()){
            case "POST":
                $response = $this->http->post($this->getPostData());
                break;
            case "POSTJSON":
                $response = $this->http->postJson(json_encode($this->getPostData()));
                break;
            default:
                $response = $this->http->get();
                break;
        }
        $this->setResponse($response);
        if($response->getErrCode() != 0 || $response->getStatusCode() != 200){
            return false;
        }
        $this->autoWriteLog();
        return $this->responseBodyToArray($bodyFormat);
    }

    public function getErrCode()
    {
        return $this->response->getErrCode();
    }

    public function getErrMsg()
    {
        return $this->response->getErrMsg();
    }

    /**
     * Notes: 格式转行 xml json转成数组
     * User: Victor
     * Date: 2021/4/9
     * Time: 10:24
     * @param $bodyFormat
     * @return array|mixed
     */
    public function responseBodyToArray($bodyFormat)
    {
        switch (strtolower($bodyFormat)){
            case "json":
                $result = $this->responseBodyJson();
                break;
            case "xml":
                $result = $this->responseBodyXml();
                break;
            default :
                $result = $this->responseBody();
        }
        return $result;
    }


    public function responseBody()
    {
        return $this->response->getBody();
    }

    public function responseBodyJson():array
    {
        return json_decode($this->response->getBody(),true);
    }

    public function responseBodyXml():array
    {
        //todo
    }

    /**
     * Notes: 自动写入日志
     * User: Victor
     * Date: 2021/3/5
     * Time: 09:53
     */
    protected function autoWriteLog()
    {
        if($this->getAutoWriteLog()){
            $logLevel = CustomLogger::LOG_LEVEL_INFO;
            if($this->response->getErrCode() != 0 || $this->response->getStatusCode() != 200){
                $logLevel = CustomLogger::LOG_LEVEL_ERROR;
            }
            RequestLog::create()->httpClient($this->url,$this->getResponse(),$this->http)
                ->setLogLevel($logLevel)
                ->writeLog();
        }
    }

    /**
     * Notes: 手动写入日志
     * User: Victor
     * Date: 2021/3/5
     * Time: 09:55
     * @param int $logLevel
     * @param RequestLog $requestLog
     */
    public function handWriteLog($logLevel = CustomLogger::LOG_LEVEL_INFO,RequestLog $requestLog)
    {
        $this->setAutoWriteLog(false);
        $requestLog->httpClient($this->url,$this->getResponse(),$this->http)
            ->setLogLevel($logLevel)
            ->writeLog();
    }


}