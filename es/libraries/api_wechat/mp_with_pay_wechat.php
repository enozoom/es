<?php namespace es\libraries\api_wechat;
/**
 * 带支付功能的微信公众号
 */
require 'helper_pay.php';
class Mp_with_pay_wechat extends Mp_wechat{
  private $pay;
  
/**
 * 初始化微信支付的相关参数
 * @param string $mchid
 * @param string $key
 * @param string $sslcert
 * @param string $sslkey
 */
  public function _init_pay($mchid, $key, $sslcert, $sslkey, $ip){
    $this->pay = new Pay($this->appid,$mchid, $key, $sslcert, $sslkey, $ip);
  }
  
  public function send_hongbao($open_id,$amount,$wishings,$share=[]){
    if( empty($this->pay) ) return false;
    $this->pay->send_hongbao($open_id, $amount, $wishings, $share);
  }
  
}

