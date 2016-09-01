<?php 
namespace es\helpers;

if( !function_exists('isMobile') ){
  /**
   * 验证手机号
   * @param unknown $mobile
   * @return number
   */
  function isMobile($mobile){
    return preg_match('|1\d{10}|',$mobile);
  }
}

if( !function_exists('isRequire') ){
  /**
   * 检查数据数组中必须包含的值
   * @param array $requires 必须的值的键数组
   * @param array $data     要查询数组
   * @return bool 全部有值则为true
   */
  function isRequire($requires=[],$data=[]){
    empty($data) && $data = $_GET;
    foreach($requires as $k){
      if(!isset($data[$k])) return FALSE;
    }
    return TRUE;
  }
}