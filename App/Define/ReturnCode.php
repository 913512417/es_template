<?php
/**
 * User: victor
 * Date: 2020/2/17
 * Time: 22:58
 * Description: 客户端返回码定义
 */
namespace App\Define;

class ReturnCode
{
    const SUCESS = 0; //成功
    const SYSTEM_ERROR = 500;//系统错误
    const TOKEN_EXP = 2; // token过期，需要重新验签
    const TOKEN_ERROR = 3; // token错误
    const NO_PERSSION = 4; // 无权限
    const FAILED = 100000; //失败

    const PARAM_ERROR = 100001;//参数错误
    const PARAM_MISS = 100002;//缺少必传参数
    const TOKEN_OUT_DATE = 100003;//token过期
    const MERCHANT_TOKEN_ERROR = 100004;//token错误
    const SIGN_ERROR = 100005;//签名错误
    const NO_ENOUGH = 100006;
    const NO_EXIST_GOODS = 100007;
    const NO_MERCHANT = 100008;
    const NO_ORDER = 100009;
    const MOBILE_ISEXIST = 100010;

    const ACCESS_FORBIDDEN = 100100;

    const REQUEST_UPPER_LIMIT = 1000101;

    const LOGIN_ERROR = 501;//登录错误

}