<?php
/**
 * User: victor
 * Date: 2020/2/27
 * Time: 10:43
 * Description:
 */

namespace App\Utility\Common;

use App\Define\ReturnCode;
use EasySwoole\Jwt\Jwt;

class JwtToken
{
    // 获取token
    public static function getToken($accountId, $data = []){
        $jwtConf = getConf('jwt_conf');
        $jwtObject = Jwt::getInstance()
            ->setSecretKey($jwtConf['secret_key'])             // 秘钥
            ->publish();
        $jwtObject->setAlg($jwtConf['alg']);                   // 加密方式
        $jwtObject->setAud($accountId);                        // 用户
        $jwtObject->setExp(time()+$jwtConf['exp']);       // 过期时间
        $jwtObject->setIat(time());                             // 发布时间
        $jwtObject->setIss($jwtConf['iss']);                    // 发行人
        $jwtObject->setJti(md5(time()));                        // jwt id 用于标识该jwt
        $jwtObject->setNbf(time()+1);                      // 在此之前不可用
        $jwtObject->setSub($jwtConf['sub']);                    // 主题
        if($data){
            // 自定义数据
            $jwtObject->setData([
                $data
            ]);
        }
        // 最终生成的token
        $token = $jwtObject->__toString();
        return $token;
    }

    // 解析token
    public static function decodeToken($token){
        $jwtConf = getConf('jwt_conf');
        try {
            // 如果encode设置了秘钥,decode 的时候要指定
            $jwtObject = Jwt::getInstance()->setSecretKey($jwtConf['secret_key'])->decode($token);
            $status = $jwtObject->getStatus();
            switch ($status)
            {
                case  1:
                    $accountId = $jwtObject->getAud();
                    $data = $jwtObject->getData();
                    return ['errcode'=>ReturnCode::SUCESS, 'Fadmin_id'=>$accountId, 'data'=> $data];
                    break;
                case  -1:
                    return ['errcode'=>ReturnCode::TOKEN_ERROR, 'msg'=>'签名错误'];
                    break;
                case  -2:
                    return ['errcode'=>ReturnCode::TOKEN_EXP];
                    break;
            }
        } catch (\EasySwoole\Jwt\Exception $e) {
            return ['errcode'=>ReturnCode::FAILED, 'msg'=>'出错了'];
        }
    }
}