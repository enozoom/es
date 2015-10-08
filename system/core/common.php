<?php defined('SYSPATH') OR exit('POWERED BY Enozoomstudio');
/*
 * 公共方法
**/

if(!function_exists('auto_loader')){
/**
 * 自动装载控制器类
 * @param string $classname
 * @return void
 */
  function auto_loader($classname){
    global $Route;
    $classname = strtolower($classname);
    strpos($classname,'\\') && $classname = substr($classname,strripos($classname, '\\')+1);
    $paths = array(SYSPATH.'core',
                   SYSPATH.'database',
                   APPPATH.'core',
                   APPPATH.'libraries',
                   SYSPATH.'libraries',
                   APPPATH.'controllers',
                   APPPATH.'controllers/'.$classname,
              );
    foreach($paths as $path){
      $path = "{$path}/{$classname}.php";
      if( file_exists($path) ){
         if(empty($Route) || $Route->cmdq['c'] != $classname){
           include $path;
           return '';
         }

      }
    }
  }
}

if(!function_exists('__shutdown')){
/**
 * 关闭页面时调用
 * @return
 */
  function __shutdown(){
    
  }  
}


if(!function_exists('__error')){
/**
 * 出现错误时调用，无法捕获致命错误。
 */
  function __error(){
    echo '出现错误';
  }  
}

if(!function_exists('redirect_url')){
/**
 * 重定向
 * @param string $url
 */  
  function redirect_url($url){
    header("Location:{$url}");exit();
  }
}

if(!function_exists('curl_post_data')){
/**
 * 通过curl,POST提交数据到远程服务器
 * @param string $url 远程服务器地址
 * @param array $post 要提交的数据
 * @param bool $is_json 返回结果为json?
 * @return mixed 如果返回结果是jsonString,则返回解密后的结果，如果异常则返回空字符串
 */  
  function curl_post_data($url,$post,$is_json=1){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);    
    $return = curl_exec($ch);
    
//    var_dump($return);
    
    curl_errno($ch) && $return = '';// 出现异常
    curl_close($ch);
    $is_json && $return = json_decode($return);
    return $return;
  }
}

if(!function_exists('curl_file_get_contents')){
/**
 * 使用curl获取网页内容
 * @param String $url 网址
 * @param bool $local 是否自动加当前域名为网址
 * @return string
 */  
  function curl_file_get_contents($url,$local=FALSE){
    empty($local) || $url=base_url($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//    curl_setopt($ch, CURLOPT_SSLVERSION, 3); //设定SSL版本
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER ,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST ,FALSE);
    $output = curl_exec($ch);
    if($output === false){
      log_msg(curl_error($ch));
    }
    curl_close($ch);     
    return $output;
  }
}


if(!function_exists('get_format_time')){
/**
 * 格式化一个UNIX时间值
 * @param int $unixtime
 * @param string $format
 * @return string
 */  
  function get_format_time($unixtime=0,$format='Y-m-d H:i:s'){
    empty($unixtime) && $unixtime = time();
    if(!is_numeric($unixtime))return $unixtime;
    return date($format,$unixtime);
  }  
}

if(!function_exists('mk_dir')){
/**
 * 生成文件夹
 * 注意要生成的文件的位置权限是否可写
 * @param string $filepath 生成的路径
 * @param bool $timedir     是否开启时间格式文件夹
 *
 * @return
 */
  function mk_dir($filepath,$timedir=FALSE){
    $timedir = $timedir?date('/Y/m'):'';
    $filepath = str_replace('//','/',$filepath.$timedir);
    if(!file_exists($filepath)){
      mkdir($filepath,0777,1);
    }
    return $filepath.'/';
  }
}

if(!function_exists('show_404')){
/**
 * 显示404页面
 * @param string $title
 * @param string $msg
 *
 * @return
 */
  function show_404($msg='',$title='404'){
    set_status_header(404);
    ob_start();
    include(APPPATH.'errors/404.php');
    $buffer = ob_get_contents();
    ob_end_clean();    
    echo $buffer;
    exit();
  }
}

if(!function_exists('show_500')){
/**
 * 显示500页面
 * @param string $title
 * @param string $msg
 *
 * @return
 */
  function show_500($msg='',$title='500'){
//    show_404();
    set_status_header(500);
    ob_start();
    include(APPPATH.'errors/500.php');
    $buffer = ob_get_contents();
    ob_end_clean();    
    echo $buffer;
    exit();
  }
}

if (!function_exists('set_status_header')){
/**
 * 设定 HTTP 报头
 * @param int $code
 *
 * @return
 */
  function set_status_header($code = 200){
    $stati = array(404=>'Not Found',500=>'Internal Server Error');
    $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;
    $text = $stati[$code];
    if (substr(php_sapi_name(), 0, 3) == 'cgi'){
      header("Status: {$code} {$text}", TRUE);
    }elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0'){
      header($server_protocol." {$code} {$text}", TRUE, $code);
    }else{
      header("HTTP/1.1 {$code} {$text}", TRUE, $code);
    }
  }
}

if(!function_exists('log_msg')){
/**
 * 记录信息
 * @param mixed  $msg
 * @param string $tit
 * @param string $dir 记录所在文件夹
 *
 * @return
 */
  function log_msg($msg='',$tit='',$dir='def'){
    ob_start();
    var_dump($msg);
    $m = ob_get_contents();
    ob_end_clean();
    $path = mk_dir('./logs/'.$dir,1);
    $file = date('Y-m-d').'.log';
    $m = '【'.get_format_time().'】'.$tit.PHP_EOL.$m.PHP_EOL.PHP_EOL;
    file_put_contents($path.$file,$m.PHP_EOL,FILE_APPEND);    
  }
}

if(!function_exists('header_utf8')){
/**
 * 设置页面UTF8的报头内容
 * @return
 */
  function header_utf8(){
    return "Content-type: text/html; charset=utf-8";
  }
}

if(!function_exists('ismobile')){
/**
 * 判断是否是手机号
 * @param int $mobi
 *
 * @return bool
 */
  function isMobile($mobi){
    $preg ='/^(1(([34578][0-9])|(47)|[8][0126789]))\d{8}$/';
    return preg_match($preg,$mobi)?TRUE:FALSE;
  }
}

if(!function_exists('generate_base64_img')){  
/**
 * 将一个图片生成图片字符串
 * @param string $imgfile
 * @param bool $is_http 是一个远程网址
 * @return string
 */
  function generate_base64_img($imgfile,$is_http=FALSE){
    if($is_http){
      strpos($imgfile,'http')===FALSE&&$imgfile = base_url($imgfile);
    }else{
      file_exists($imgfile)  || $imgfile = '.'.$imgfile;
      if(!file_exists($imgfile)){
        return '';
      }      
    }

    $str = base64_encode(file_get_contents($imgfile));
    $img_attr = getimagesize($imgfile);
    return sprintf('data:%s;base64,%s',$img_attr['mime'],$str);
  } 
}

if(!function_exists('get_addrbyip')){
/**
 * 新浪IP接口,如果找不到地址则返回IP地址
 * @param String $ip
 * @return String
 */
  function get_addrbyip($ip){
    $addr = @file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=".$ip); 
    $addr = json_decode($addr,1);
    return $addr['ret']>0? $addr['province'].' '.$addr['city']:$ip;
  }  
}

if(!function_exists('clean_htmltag')){
/**
 * 去html字符串和换行符
 * @param $str string 需要进行转换的含有html标签的字符串
 * @return string
 */
  function clean_htmltag($str){
    return preg_replace(array('/(<\/?)(\w+)([^>]*>)/',"/\n/","/\r\n/","/\r/"),array('','',''),$str);
  }
}

if(!function_exists('clean_wordblank')){
/**
 * 去字与字之间的空白.
 * @param $str string
 * @return string
 */
  function clean_wordblank($str){
    $str = preg_replace('|(\s*)(\S+)(\s*)(\S+)(\s*)|','$2$4',$str); 
    // 解决中文空格和换表符无法正确匹配的问题
    $str = str_replace(array('  ','　','  ','　'), array('','','',''), $str);
    return remove_invisible_characters($str); 
  }  
}

if(!function_exists('clean_htmlblank')){
/**
 * 去html的空白(标签间的空白和换行,对于非标签间的无能为力)
 * @param $str string
 * @return string
 */
  function clean_htmlblank($str){
    return preg_replace(array('/\n/','/>\s*([^\s]*)\s*</', '/<!--[^\[>]*>/',"/\r\n/","/\r/"),
                    array('','>$1<','','',''),$str);
  }
}

if (!function_exists('remove_invisible_characters')){
/**
 * 去除非法字符
 * @access public
 * @param  string $str
 * @return string $url_encoded
 */
  function remove_invisible_characters($str, $url_encoded = TRUE)
  {
    $non_displayables = array();
    
    if ($url_encoded){
      $non_displayables[] = '/%0[0-8bcef]/';  // url encoded 00-08, 11, 12, 14, 15
      $non_displayables[] = '/%1[0-9a-f]/';  // url encoded 16-31
    }
    
    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';  // 00-08, 11, 12, 14-31, 127

    do{
      $str = preg_replace($non_displayables, '', $str, -1, $count);
    }while ($count);

    return $str;
  }
}  

if (!function_exists('random_string')){
/**
 * 产生随机数
 * @param string $type alnum:数字+大小写字母;numeric:数字;alpha:大小写字母
 * @param int    $len
 *
 * @return string
 */
  function random_string($type='alnum',$len=6){
    $numeric = '0123456789';
    $alpha = 'abcdefghijklmnopqistuvwxyzABCDEFGHIJKLMNOPQISTUVWXYZ';
    $alnum = $numeric.$alpha;
    $_str = array();
    
    $_type = $$type;
    $str = '';
    for($i=0; $i < $len; $i++){
      $str .= substr($_type, mt_rand(0, strlen($_type) -1), 1);
    }

    return $str;
  }
}

if(!function_exists('base_url')){
/**
 * 获取当家uri中的host部分，与传入的参数组成网址
 * @param string $url
 * @return string
 */  
  function base_url($url=''){
    return 'http://'.str_replace('//','/',$_SERVER['HTTP_HOST'].'/'.$url);
  }
}
