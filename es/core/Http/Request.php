<?php
namespace es\core\Http;

trait Request{
    
  /**
   * 获取当家uri中的host部分，与传入的参数组成网址
   * @param string $url
   * @return string
   */
  protected function baseUrl($url=''){
      return strpos($url, 'http://')===FALSE?('http://'.str_replace('//','/',$_SERVER['HTTP_HOST'].'/'.$url)):$url;
  }
  /**
   * 请求的方法
   * @param string $method 如果$method不为空则判断请求方法是否与$method一致。
   * @return string|bool
   */
  protected function reqestMethod($method='')
  {
    $m = strtolower($_SERVER['REQUEST_METHOD']);
    return empty($method)?$m:$m==$method;
  }

/**
 * 重写$_GET
 */
  protected function rewriteGet()
  {
    $uri = $_SERVER['REQUEST_URI'];
    isset($_SERVER['HTTP_X_ORIGINAL_URL']) && $uri = $_SERVER['HTTP_X_ORIGINAL_URL'];
    
    if( ($idx = strpos($uri,'?')) && strpos($uri,'=') ){// $_GET有参值
      $_get = [];
      $uri = substr( str_replace('amp','',$uri) ,$idx+1 );
        
      foreach( explode('&',$uri) as $kv ){
        $_kv = explode('=',$kv);
        $_get[$_kv[0]] = $_kv[1];
      }
    }
    $_GET = empty($_get)?null:$_get;
  }

/**
 * 远程get获取
 * @param string $url
 */
  protected function curlGet($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->baseUrl($url));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    //    curl_setopt($ch, CURLOPT_SSLVERSION, 3); //设定SSL版本
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER ,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST ,FALSE);
    $output = curl_exec($ch);
    if($output === false){
      if(trait_exists( '\\es\\core\\Toolkit\\Config' )){
        $this->getConfigs('logger')->debug(curl_error($ch));
      }
    }
    curl_close($ch);
    return $output;
  }
  
/**
 * 远程post提交
 * @param string $url
 * @param array|string $post
 * @param number $is_json
 */
  protected function curlPost($url,$post,$is_json=1)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->baseUrl($url));
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,is_array($post)?http_build_query($post):$post);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $return = curl_exec($ch);
    if(trait_exists( '\\es\\core\\Toolkit\\Config' )){
      //$this->getConfigs('logger')->debug($return);
    }
    
    curl_errno($ch) && $return = '';// 出现异常
    curl_close($ch);
    $is_json && $return = json_decode($return);
    return $return;
  }
}