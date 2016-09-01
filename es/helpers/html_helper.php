<?php
namespace es\helpers;
defined('APPPATH') OR exit('POWERED BY Enozoomstudio');
/**
 * FIXED
 * 2015年4月29日10:40:15 Joe
 * 增加对外部引用css,js的支持，如:可以生出<link href="http://cdn.fcmayi.com/pure/pure-min.css" rel="stylesheet">
 * 2015年5月3日21:59:05 Joe
 * 再次修复上一个BUG
 * 2015年5月11日14:19:48 Joe
 * 再次修复上一个BUG
 * 2015年5月24日12:33:28
 * 增加对移动浏览器的设定meta
 * 增加对搜索引擎的屏蔽
 * 2015年9月10日16:24:10
 * 增强对外部引用的支持，如:base,/theme/awesome/min,index.css
 * 可以解析出
 * <link href="/theme/awesome/min.css" rel="stylesheet">
 * <link href="/min/base,index.css" rel="stylesheet">
 * 2015年9月13日16:36:22
 * 增加对IE9以下的IE浏览器支持：
 * <!--[if lt IE 9]><script src="/theme/public/js/html5div.min.js"></script><![endif]-->
 * <!--[if lt IE 9]><script src="/theme/public/js/selectivizr-min.js"></script><![endif]-->
 * 2015年11月23日11:50:37
 * 移除通用的css,js改用cdn
 */

if(!function_exists('generate_html5_head')){
/**
* 生成html5 的doctype,html,head,body部分
*
* @param string $title <title>
* @param string $css <link> 
* @param string $description <meta name="description">
* @param string $keywords <meta name="keywords">
* @param bool $viewport 页面是否为手机端页面
* @param bool $noindex  页面是否让让搜索引擎抓取
* @return string HTMLString
*/
  function generate_html5_head($title,$css=FALSE,$description=FALSE,$keywords=FALSE,$viewport=FALSE,$noindex=FALSE){
    
    $html5 = '<!DOCTYPE html><html lang="zh-cmn-Hans"><head><meta charset="utf-8" />';  
    $html5 .= "<title>$title</title>";
    $html5 .= '<meta name="renderer" content="webkit"> ';
    $html5 .= '<meta name="author" content="'.ES_AUTHOR.'" />';
    if($viewport){
      $html5 .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">';
      $html5 .= '<meta content="yes" name="apple-mobile-web-app-capable">';
      $html5 .= '<meta content="black" name="apple-mobile-web-app-status-bar-style">';
      $html5 .= '<meta content="telephone=no" name="format-detection">';
    }else{
      //增加对IE9一下的浏览器HTML5标签支持
      $html5 .= head_script('//cdn.fcmayi.com/lt.ie.9/html5shiv/3.7.3/html5shiv.min','lt IE 9',1);
      $html5 .= head_script('//cdn.fcmayi.com/lt.ie.9/selectivizr/1.0.2/selectivizr-min','lt IE 9',1);
    }
    if($noindex){
      $html5 .= '<meta name="robots" content="noindex,nofollow" />';
    }    
    $keywords && $html5 .= "<meta name=\"keywords\" content=\"$keywords\" />";
    $description && $html5 .= "<meta name=\"description\" content=\"$description\" />";
    empty($css) || $html5 .= resolve_http($css);
    
    return $html5.'</head><body>';
  }  
}


if(!function_exists('generate_html5_foot')){
/**
* 生成html5 的body,html闭合部分
*/
  
  function generate_html5_foot($js=''){
    $html = '';
    empty($js) || $html .= resolve_http($js,'js');
    return $html."</body></html>";
  }
}

if(!function_exists('head_script')){
/**
* 导入js文件,不需要添加.js后缀,支持本域使用数组形式导入JS文件,不支持其他域数组形式导入.
* @example:
* 表现在页面html代码为:
* 1.<script src='urjsfile.js'></script>
* 2.
*  <!--[if $ie]>
*    <script src='urjsfile.js'></script>
*  <![endif]-->
* 
* @param $file array | 文件
* 如
* @param $ie 对IE进行兼容
* $ie = 'IE,IE 6,IE 7,IE 8,IE 9,gte IE 8,lt IE 9,lte IE 7,gt IE 6,!IE'
* 如head_script('global','lt IE 9');
* @param $foreign 是否来自外面的链接
* @return HTMLString
* 
*/

  function head_script($file,$ie=FALSE,$foreign=FALSE){
    empty($dir) && $dir='public';
    $script = sprintf('<script src="%s.js"></script>',$foreign?$file:'/min/'.$file);
    $js = empty($ie)?$script:'<!--[if '.$ie.']>'.$script.'<![endif]-->';  
    return $js;
  }
}

if(!function_exists('head_link')){
/**
* 导入style文件,不需要添加.css后缀,支持本域使用数组形式导入CSS文件,不支持其他域.
*
* @param array $file 文件
* @param $ie 对IE进行兼容
* @param $foreign 是否来自外面的链接
* @return HTMLString
* 
*/
  
  function head_link($file,$ie=FALSE,$foreign=FALSE){
    empty($dir) && $dir ='public';
    $href = $file.'.css';
    $foreign ||$href = sprintf('%s.css?esv=%.7f','/min/'.$file,ES_VERSION);
    
    $link = sprintf('<link href="%s" rel="stylesheet">',$href);
    $css = empty($ie)?$link:'<!--[if '.$ie.']>'.$link.'<![endif]-->';  
    return $css;
  }
}


if(!function_exists('_attr')){
/**
* 将属性连成字符串
* @param array $attr 
* @return HTMLString
*/
  function _attr($attr){
    $arr = [];  $htm = '';
    if( !empty($attr) && is_string($attr) && strpos($attr,':')){
      $ary = explode(',',$attr);
      foreach($ary as $k_v){
        $kv = explode(':',$k_v);
        $arr[$kv[0]] = $kv[1];
      }
    }else{
      $arr = [];
    }
    
    foreach($arr as $k=>$v){
      $htm .= $k.'="'.str_replace('"','',$v).'" ';
    }
    return $htm;
  }
}

if(!function_exists('a_cmdq')){
/**
* 生成一个cmdq参数格式的A链接
* @param string $c
* @param string $m
* @param string $d
* @param string $q
* 
* @return
*/  
  function a_cmdq($c='home',$m='index',$d='public',$q=''){    
    return native_a_cmdq($c,$m,$d,$q);
  }
  function a_cmdq_($c='home',$m='index',$d='public',$q=''){
    return str_replace('&amp;','&',a_cmdq($c,$m,$d,$q));
  }
  function native_a_cmdq($c='home',$m='index',$d='public',$q=''){    
    $url = sprintf('/%s/%s/%s/%s',$d,$c,$m,$q);
    return $url;
  }
}


if(!function_exists('resolve_http')){
/**
* 分解出含有外边引用的文件
* @param string $files 文章
* @param string $type 文件类型，css,或者js
* @return string
*/
  function resolve_http($files,$type='css'){
    $html = '';
    // 含http的外部链接，如http://cdn.fcmayi.com
    preg_match_all('@,?http[^,]+,?@',$files,$matchs);
    $method = '\\es\\helpers\\head_'.($type == 'css'?'link':'script');
    foreach($matchs[0] as $match){
      $files = str_replace($match,',',$files);
      $foreign = str_replace(',', '', $match);
      $html .= $method($foreign,FALSE,TRUE);
    }
    
    // 不含http但是不在public/css文件夹下
    preg_match_all('@,?(\/[^,]+)@',$files,$matchs);
    foreach($matchs[1] as $m){
     $files = str_replace($m,',',$files);
     strpos($method, __NAMESPACE__)===FALSE && $method = __NAMESPACE__.'\\'.$method;
     $html .= $method($m,FALSE,TRUE);
    }
    
    $files = preg_replace('/,+/',',',$files);// 抽取掉外部引用后，可能造成多个，同时存在
    strpos($files,',')===0 && $files = substr($files,1);// 有可能出现第一个字母是”，“的情况。
    $files = preg_replace('@(,)$@', '', $files);// 末尾可能是“，”的情况
    
    strpos($method, __NAMESPACE__)===FALSE && $method = __NAMESPACE__.'\\'.$method;
    empty($files) || $html .= $method($files);
    return $html;
  }
}