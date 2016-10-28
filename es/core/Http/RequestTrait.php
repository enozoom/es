<?php
namespace es\core\Http;

trait RequestTrait{
    
  /**
   * 获取当家uri中的host部分，与传入的参数组成网址
   * @param string $url
   * @return string
   */
  protected function baseUrl($url=''){
      if( strpos($url, 'http://') === FALSE && strpos($url, 'https://') === FALSE ){
          $url = 'http://'.str_replace('//','/',$_SERVER['HTTP_HOST'].'/'.$url);
      }
      return $url;
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
   * 将提交的json转化成对象
   */
  protected function phpInputData(){
      $data = file_get_contents('php://input');
      return empty($data)?null:json_decode($data);
  }
  
/**
 * 重写$_GET
 */
  protected function rewriteGet()
  {
    $uri = $_SERVER['REQUEST_URI'];
    isset($_SERVER['HTTP_X_ORIGINAL_URL']) && $uri = $_SERVER['HTTP_X_ORIGINAL_URL'];
    $uri = parse_url($uri,PHP_URL_QUERY);
    $_get = [];
    if( !empty($uri) ){// $_GET有参值
      foreach( explode('&',str_replace('&amp', '&', $uri) ) as $kv ){
        if($_kv = explode('=',$kv)){
          (!empty($_kv[0]) && !empty($_kv[1])) && $_get[$_kv[0]] = $_kv[1];
        }
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
    
    if(  $output === false){
      if(trait_exists( '\\es\\core\\Toolkit\\ConfigTrait' )){
        $this->getConfigs('logger')->debug(curl_error($ch));
      }
    }
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
    
    curl_errno($ch) && $return = '';// 出现异常
    curl_close($ch);
    $is_json && $return = json_decode($return);
    return $return;
  }
}