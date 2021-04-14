<?php
/**
 * User: victor
 * Date: 2020/2/17
 * Time: 23:13
 * Description:
 */
namespace App\Utility\Common;

class ReturnMsg
{
    const RERTURN_MSG = [
        ReturnCode::SUCESS => "成功",
        ReturnCode::FAILED => "操作失败",

        ReturnCode::PARAM_ERROR => "参数错误",
        ReturnCode::PARAM_MISS => "缺少必传参数",
        ReturnCode::SIGN_ERROR => '签名错误',

        ReturnCode::SYSTEM_ERROR => '系统错误',
    ];
}