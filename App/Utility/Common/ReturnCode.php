<?php
/**
 * User: victor
 * Date: 2020/2/17
 * Time: 22:58
 * Description: 客户端返回码定义
 */
namespace App\Utility\Common;

class ReturnCode
{
    const SUCESS = 0; //成功
    const FAILED = 1; //失败

    //400~499 客户端提交参数的错误
    const PARAM_MISS = 400;//缺少必传参数
    const PARAM_ERROR = 401;//参数错误
    const SIGN_ERROR = 402;//签名错误

    const SYSTEM_ERROR = 500;//系统错误


}