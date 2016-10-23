<?php
namespace es\core\Http;

use es\core\Toolkit\InjectionTrait;
use es\core\Toolkit\ConfigTrait;
use es\core\Http\HeaderTrait;

trait ResponseTrait{
  use InjectionTrait,ConfigTrait,HeaderTrait;
  
  protected function show_503($msg='',$tit='503 Service Unavailable')
  {
    $this->_tpl_err($msg,$tit);
  }
  
  protected function show_404($msg='',$tit='404 Not Found')
  {
    $this->_tpl_err($msg,$tit,404);
  }
  
  protected function show_403($msg='Directory access is forbidden.',$tit='403 Forbidden')
  {
    $this->_tpl_err($msg,$tit,403);
  }
  
  protected function _tpl_err($msg,$tit='503 Service Unavailable',$status=503){
    if( $this->getConfig('debug') )
    {
      $this->tpl_err($msg,$tit,$status);
    }
    else
    {
      $this->tpl_err('<p>当前页面禁止访问！</p><p><small>'.ES_POWER.'</small></p>','禁止访问',403);
    }
  }
  
/**
 * 输出到浏览器
 * @param string $str         输出内容
 * @param string $type        输出类型
 * @param int $httpcacheHours 缓存时间
 */
  protected function render($str='',$type='html',$httpcacheHours=24)
  {
    $compress = $this->getConfig('compress_outpage');
    $flag = $compress && strlen($str)>1024 && extension_loaded('zlib');
    $this->httpCache($httpcacheHours);
    $this->httpMime($type);
    $flag && ob_start('ob_gzhandler');
      echo $str;
    $flag && ob_end_flush();
  }

}