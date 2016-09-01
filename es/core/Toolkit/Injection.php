<?php
/**
 * 将参数注入到页面
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月22日上午10:11:36
 */
namespace es\core\Toolkit;

trait Injection{
/**
 * 将变量注入到一个模板,并输出模板内容和状态码
 * @param string $tamplate
 * @param array $args
 * @param number $status
 */
  protected function byTamplate($tamplate,Array $args=[],$status=200){
    http_response_code($status);
    echo $this->loadTamplate($tamplate,$args);
    exit();
  }
  
  protected function loadTamplate($tamplate,Array $args=[]){
    extract($args);
    $path = APPPATH.'views/'.$tamplate.'.php';
    file_exists( $path ) || $this->tpl_err($path.'不存在','视图文件不存在');
    ob_start();
    include( $path );
    $buffer = ob_get_contents();
    ob_end_clean();
    return $buffer;
  }
  
  protected function tpl_err($msg,$tit='503 Service Unavailable',$status=503){
    $this->byTamplate('errors/'.$status,['msg'=>$msg,'tit'=>$tit],$status);
  }
}