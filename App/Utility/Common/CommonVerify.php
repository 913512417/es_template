<?php
/**
 * User: victor
 * Date: 2019/8/13
 * Time: 16:39
 * Description:公共验证了类
 */
namespace App\Utility\Common;

trait CommonVerify
{

    /**
     * 验证手机号
     * @param $mobile
     * @return bool
     */
    public function mobile($mobile){
        if(preg_match('/^1[3456789]\d{9}$/',$mobile)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 验证密码是否符合规范
     * @param $password
     * @return bool
     */
    public function password($password){
        if (!preg_match("/^[a-zA-Z]{1}[a-zA-Z0-9]+$/", $password)) {
            return '密码首位必须为字母且只能有字母和数字组成';
        }
        $lenPwd = strlen($password);
        if ($lenPwd < 6 || $lenPwd > 40) {
            return  '密码长度必须在6~40之间';
        }
        return true;
    }

    /**
     * 校验url
     * @param $url
     * @return bool
     */
    public function checkDomain($url){
        //todo 验证是否域名是否是在白名单内
        return true;
    }

    /**
     * 判断是否全是中文
     * @param $str
     * @return bool
     */
    public function isAllChinese($str){
        //新疆等少数民族可能有·
        if(strpos($str,'·')){
            //将·去掉，看看剩下的是不是都是中文
            $str=str_replace("·",'',$str);
            if(preg_match('/^[\x7f-\xff]+$/', $str)){
                return true;//全是中文
            }else{
                return false;//不全是中文
            }
        }else{
            if(preg_match('/^[\x7f-\xff]+$/', $str)){
                return true;//全是中文
            }else{
                return false;//不全是中文
            }
        }
    }

    /**
     * 验证身份证
     * @param $idcard
     * @return bool
     */
    function isIdCard($idcard) { // 检查是否是身份证号
        // 只能是18位
        if(strlen($idcard)!=18){
            return false;
        }
        // 转化为大写，如出现x
        $idcard = strtoupper($idcard);
        // 取出本体码
        $idcard_base = substr($idcard, 0, 17);
        // 取出校验码
        $verify_code = substr($idcard, 17, 1);
        // 加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        // 校验码对应值
        $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        // 根据前17位计算校验码
        $total = 0;
        for($i=0; $i<17; $i++){
            $total += substr($idcard_base, $i, 1)*$factor[$i];
        }
        // 取模
        $mod = $total % 11;
        // 比较校验码
        if($verify_code == $verify_code_list[$mod]){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 验证银行卡
     * @param $bankNum
     * @return bool
     */
    public function checkBankNum($bankNum)
    {
        $arr_no = str_split($bankNum);
        $last_n = $arr_no[count($arr_no)-1];
        krsort($arr_no);
        $i = 1;
        $total = 0;
        foreach ($arr_no as $n){
            if($i%2==0){
                $ix = $n*2;
                if($ix>=10){
                    $nx = 1 + ($ix % 10);
                    $total += $nx;
                }else{
                    $total += $ix;
                }
            }else{
                $total += $n;
            }
            $i++;
        }
        $total -= $last_n;
        $total *= 9;
        if($last_n == ($total%10)){
            $instance  = \EasySwoole\EasySwoole\Config::getInstance();
            $bankList=$instance->getConf('banknum_prefix');
            $res = false;
            foreach ($bankList as $v){
                if(strpos($bankNum,$v)===0){
                    $res = true;
                    break;
                }
            }
            return $res;
        }else{
            return false;
        }

    }

    /**
     * 判断虚拟号码
     * 虚拟运营商号段为167、170、171号段
     * 联通为1704、1707、1708、1709、整个171号段和整个167号段；
     * 电信为1700、1701、1702；
     * 移动为1705、1703、1706。
     * @param $value
     * @return bool
     */
    public function checkVirtualPhone($value){
        $cx = "/^(167|170|171|)\d{8}$/";
        if (preg_match($cx, $value)) {
            return true;
        }
        return false;
    }
}