<?php
/**
 * User: victor
 * Date: 2020/2/17
 * Time: 23:13
 * Description:
 */
namespace App\Define;

class ReturnMsg
{
    const RERTURN_MSG = [
        ReturnCode::SUCESS => "成功",
        ReturnCode::FAILED => "操作失败",

        ReturnCode::PARAM_ERROR => "参数错误",
        ReturnCode::PARAM_MISS => "缺少必传参数",
        ReturnCode::SIGN_ERROR => '签名错误',

        ReturnCode::SYSTEM_ERROR => '系统错误',

        ReturnCode::TOKEN_ERROR => 'token错误',
        ReturnCode::TOKEN_OUT_DATE => 'token已过期',
        ReturnCode::MERCHANT_TOKEN_ERROR => 'token错误',
        ReturnCode::NO_ENOUGH => '金额不足',
        ReturnCode::NO_EXIST_GOODS => '无此商品',
        ReturnCode::NO_MERCHANT => '此商户号不存在',
        ReturnCode::NO_ORDER => '无此订单号',
        ReturnCode::ACCESS_FORBIDDEN => '禁止访问',
        ReturnCode::REQUEST_UPPER_LIMIT => '已达到接口请求上限',

        ReturnCode::LOGIN_ERROR => '登录错误',
        ReturnCode::MOBILE_ISEXIST => '手机号已存在',

    ];
}