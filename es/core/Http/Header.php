<?php
namespace es\core\Http;

trait Header{
  
/**
  * http浏览器响应缓存
  * @param int $hour 缓存时长(小时)
  */
  protected function httpCache($hour=24)
  {
    if($hour>0 && !headers_sent()){
      $time = $hour*3600;
      header ("Last-Modified: " .gmdate("D, d M Y H:i:s", time() )." GMT");
      header ("Expires: " .gmdate("D, d M Y H:i:s", time()+$time )." GMT");
      header ("Cache-Control: max-age=$time");
    }
  }
  
/**
 * Mimes
 * @param string $type
 * @param bool $utf8 同时输出utf8
 */
  protected function httpMime($type='html',$utf8=FALSE){
    $m = 'text/html; charset=utf-8';
    switch ($type){
      case 'atom':
        $m = 'application/atom+xml';
      break;
      case 'css': case 'less':
        $m = 'text/css';
      break;
      case 'js':case 'javascript':
        $m = 'text/javascript';
      break;
      case 'json':
        $m = 'application/json';
      break;
      case 'pdf':
        $m = 'application/pdf';
      break;
      case 'rss':
        $m = 'application/rss+xml; charset=utf-8';
      break;
      case 'xml':
        $m = 'text/xml';
      break;
      case 'txt':case 'text':
        $m = 'text/plain';
      break;
      case 'zip':
        $m = 'application/zip';
      break;
    }
    headers_sent() || header('Content-type: '.$m.(empty($utf8)?'':'; charset=utf-8') );
  }
  

/**
 * 跳转
 * @param string $url
 */
  protected function redirect($url='')
  {
    if( !empty($url) )
    {
      header('Location: '.$url);
      exit();
    }
  }
  
}