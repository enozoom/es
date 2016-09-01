<?php
namespace es\core\Http;

use es\core\Http\Response;
use es\core\Http\Request;
/**
 * 将请求翻译成[c=>controller,m=>method,d=>directory,q=querystring]
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月21日下午5:11:43
 */

class Cmdq{
  use Response,Request;
  
  public function get(){
    $cmdq = $this->resolve();
    return (object)$cmdq;
  }
  
  private function resolve(){
    $routes = $this->getConfigs('routes');
    // 请求字符串
    $query = function(){
      $q ='';
      isset($_SERVER['QUERY_STRING']) && $q = $_SERVER['QUERY_STRING'];
      isset($_SERVER['SCRIPT_URL']) && $q = $_SERVER['SCRIPT_URL'];//SAE
      empty($query) && $q = $_SERVER['REQUEST_URI'];
      return $q;
    };
    $query = $query();
    
    // 重写GET
    $this->rewriteGet();
    // 过滤掉可能存在querystring?a=b的GET形式，GET已经重写。
    ($i = strpos($query, '?')) !== FALSE && $query = substr($query,0,$i);
    
    // 动态网址解析
    $dynamic = function() use (&$query,$routes){
      if(!isset($_GET['c']) && !isset($_GET['m']) && !isset($_GET['d'])){
        foreach($routes as $route){
          if($route->pattern == 'default'){
            $query = $route->cmdq;
            break;
          }
        }
      }
      if($query == '/'){// 如果这种路径写法 /?d=public&c=test&m=aaa&q=1,2,3,4
        $q = '';
        foreach($_GET as $k=>$v) $q .= "&{$k}={$v}";
        $query = substr($q,1);
      }
    };
    // 静态网址解析
    $static = function() use ($query,$routes){
      $suffix = $this->getConfig('suffix');
      // 不需要去的后缀
      $mimes = array('css','js','less');
      if(!empty($suffix)){
        $mimes[] = $suffix;
        // 去后缀
        $query = preg_replace('|\.'.$suffix.'$|','',$query);
      }
      // 如果第一个字符是/，去掉
      substr($query,0,1) === '/' && $query = substr($query,1);
      // 如果最后一个字符是/， 去掉
      substr($query,-1,1) === '/' && $query = substr($query,0,strlen($query)-1);
      // 分割参数
      $args = explode('/',$query);
      
      // 如果未使用配置中的后缀
      if( ($i=strrpos( $last_segement=$args[count($args)-1],'.') )>0){
        in_array(substr($last_segement,$i+1),$mimes) ||  $this->show_503('未使用配置中的后缀');
      }
      
      // 与路由配置文件进行匹配
      foreach($routes as $route){
        $preg = "#^{$route->pattern}$#";
        if(preg_match($preg,$query)){
          $url = preg_replace($preg,$route->cmdq,$query);
          //无特殊参数，或者是min压缩
          if( strpos($url,'/')===FALSE || strpos($url,'c=min')!==FALSE ){
            return $url;
          }
        }
      }
      
      
      // 如果路由配置文件中没有配置，则尝试匹配默认
      // 具体规则为 directory/controller/method/args
      $url = 'd=%s&c=%s&m=%s&q=%s';
      
      switch(count($args)){
        case 1:// controller
          $args = explode( '/',"esweb/{$args[0]}/index/" );
        case 2:// directory/controller
          $args = explode( '/',"{$args[0]}/{$args[1]}/index/" );
        case 3:// directory/controller/method
          $args[3] = '';
        case 4:// directory/controller/method/args
      
        default:// directory/controller/method/arg1/arg2/arg3...
          if(count($args)>4){
            $q = '';
            for($i=3;$i<count($args);$i++) $q .= ','.$args[$i];
            $args[3] = substr($q,1);
          }
          return sprintf($url,$args[0],$args[1],$args[2],$args[3]);
          break;
      }
    };
    $preg = function($query){
      $cmdq = array('c'=>'home','m'=>'index','d'=>'esweb','q'=>'');
      $str = str_replace('amp;','',$query);
      if( strpos($str,'&') == FALSE ){
        $_tmp = explode('=',$str);
        $cmdq[$_tmp[0]] = $_tmp[1];
      }else{
        $_tmp = explode( '&',$str );
        foreach($_tmp as $_tm){
          $_t = explode('=',$_tm);
          isset( $cmdq[$_t[0]] ) && $cmdq[$_t[0]] = $_t[1];
        }
      }
      return $cmdq;
    };
    
    
    // 解析出 cmdq格式的参数url字符串，并对应$this->cmdq
    if( strlen($query)==1 || strpos($query,'/') === FALSE ){
      $dynamic();
    }else{// 可能设置的是静态路径
      $query = $static();
    }
    return $preg($query);
  }
}