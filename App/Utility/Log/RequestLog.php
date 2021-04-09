<?php
namespace App\Utility\Log;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\Request;
use EasySwoole\HttpClient\Bean\Response;
use EasySwoole\Http\Response as ServerResponse;
use EasySwoole\HttpClient\HttpClient;


class RequestLog
{
    /** @var string */
    protected $clientIp = "";
    /** @var string */
    protected $method;
    /** @var string */
    protected $url;
    /** @var string  */
    protected $requestBody;
    /** @var mixed  */
    protected $header;
    /** @var string  */
    protected $responseBody;
    /** @var mixed */
    protected $statusCode;
    /** @var mixed */
    protected $errCode;
    /** @var string */
    protected $errMsg;
    /** @var string */
    protected $customMsg;
    /** @var mixed */
    protected $customData;
    /** @var int */
    protected $logLevel = CustomLogger::LOG_LEVEL_INFO;

    public static function create()
    {
        return new static();
    }

    public function httpClient(string $url,Response $response,HttpClient $request)
    {
        $this->setUrl($url)
            ->setStatusCode($response->getStatusCode())
            ->setErrCode($response->getErrCode())
            ->setErrMsg($response->getErrMsg())
            ->setMethod($response->getRequestMethod())
            ->setRequestBody($response->getRequestBody())
            ->setHeader($request->getHeader())
            ->setResponseBody($response->getBody());
        return $this;
    }

    public function httpServer(Request $request,ServerResponse $response)
    {
        $clientIp = $request->getHeaders()['x-real-ip'][0];
        $this->setUrl($request->getUri()->__toString())
            ->setMethod($request->getMethod())
            ->setClientIp($clientIp)
            ->setHeader($request->getHeaders())
            ->setResponseBody($response->getBody())
            ->setStatusCode($response->getStatusCode())
            ->setRequestBody($request->getBody());
        return $this;
    }


    public function writeLog()
    {
        $log = $this->logHead();
        $log .= $this->logHeader();
        $log .= $this->logRequestBody();
        $log .= $this->logResponseBody();
        $log .= $this->logCustomMsg();
        $log .= $this->logCustomData();
        $log .= $this->logError();
        Logger::getInstance()->log($log,$this->getLogLevel());
    }

    protected function logHead()
    {
        return $this->getStatusCode()." ".$this->getClientIp()." ".$this->getMethod()." ".$this->getUrl()."\n";
    }

    protected function logHeader()
    {
        $log = "[ HEADER ]\n";
        $log .= $this->getHeader()."\n";
        return $log;
    }

    protected function logRequestBody()
    {
        $log = "[ REQUEST_BODY ]\n";
        $log .= $this->getRequestBody()."\n";
        return $log;
    }

    protected function logResponseBody()
    {
        return "[ RESPONSE_BODY ]\n".$this->getResponseBody()."\n";
    }

    protected function logError()
    {
        if ($this->getErrCode()){
            return "[ ERROR ]\n".$this->getErrCode()." ".$this->getErrMsg()."\n";
        }
        return "";
    }
    protected function logCustomMsg()
    {
        if ($this->getCustomMsg()){
            return "[ CUSTOM_MSG ]\n".$this->getCustomMsg()."\n";
        }
        return "";
    }

    protected function logCustomData()
    {
        if ($this->getCustomData()){
            return "[ CUSTOM_DATA ] ".$this->getCustomData()."\n";
        }
        return "";
    }


    /**
     * @return string
     */
    protected function getClientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * @param string $clientIp
     * @return RequestLog
     */
    protected function setClientIp(string $clientIp): RequestLog
    {
        $this->clientIp = $clientIp;
        return $this;
    }

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return RequestLog
     */
    protected function setMethod(string $method): RequestLog
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    protected function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return RequestLog
     */
    protected function setUrl(string $url): RequestLog
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    protected function getRequestBody():?string
    {
        return $this->requestBody;
    }

    /**
     * @param mixed $requestBody
     * @return RequestLog
     */
    protected function setRequestBody($requestBody): RequestLog
    {
        if (is_array($requestBody) || is_object($requestBody)){
            $requestBody = json_encode($requestBody);
        }
        $this->requestBody = $requestBody;
        return $this;
    }

    /**
     * @return string
     */
    protected function getHeader(): ?string
    {
        return $this->header;
    }

    /**
     * @param mixed $header
     * @return RequestLog
     */
    protected function setHeader($header): RequestLog
    {
        if (is_array($header)){
            $header = json_encode($header);
        }
        $this->header = $header;
        return $this;
    }

    /**
     * @return string
     */
    protected function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    /**
     * @param mixed $responseBody
     * @return RequestLog
     */
    protected function setResponseBody($responseBody): RequestLog
    {
        if (is_array($responseBody)){
            $responseBody = json_encode($responseBody);
        }
        $this->responseBody = $responseBody;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     */
    protected function setStatusCode($statusCode): RequestLog
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getErrCode()
    {
        return $this->errCode;
    }

    /**
     * @param mixed $errCode
     */
    protected function setErrCode($errCode): RequestLog
    {
        $this->errCode = $errCode;
        return $this;
    }

    /**
     * @return string
     */
    protected function getErrMsg(): string
    {
        return $this->errMsg;
    }

    /**
     * @param string $errMsg
     */
    protected function setErrMsg(string $errMsg): RequestLog
    {
        $this->errMsg = $errMsg;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * 自定义数据
     * @param mixed $customData
     * @return RequestLog
     */
    public function setCustomData($customData)
    {
        if (is_array($customData)){
            $customData = json_encode($customData);
        }
        $this->customData = $customData;
        return $this;
    }
    /**
     * @return int
     */
    public function getLogLevel(): int
    {
        return $this->logLevel;
    }

    /**
     * @param int $logLevel
     * @return RequestLog
     */
    public function setLogLevel(int $logLevel): RequestLog
    {
        $this->logLevel = $logLevel;
        return $this;
    }


    /**
     * @return string
     */
    public function getCustomMsg(): string
    {
        return $this->customMsg;
    }

    /**
     * 设置自定义错误
     * @param string $customMsg
     * @return RequestLog
     */
    public function setCustomMsg(string $customMsg): RequestLog
    {
        $this->customMsg = $customMsg;
        return $this;
    }
}