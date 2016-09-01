<?php
namespace es\core\Toolkit;

use \es\core\Toolkit\Config;
trait Auth{
    use Config;
    // 生成签名
    protected function generateSign($str)
    {
        return password_hash($str,PASSWORD_DEFAULT);
    }
    // 验证签名
    protected function validateSign($str,$hash)
    {
        return password_verify($str,$hash);
    }
    
    // 表单签名
    protected function formSign(){
        return password_hash(date('Y-m-dW'),PASSWORD_DEFAULT);
    }
    
    // 表单验证
    protected function validateFormSign($hash)
    {
        return password_verify(date('Y-m-dW'),$hash);
    }
    
    protected function ip()
    {
        $ip = '';
        if( !empty($_SERVER['HTTP_CLIENT_IP']) ){
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }elseif( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ){
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}