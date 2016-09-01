<?php
namespace es\core\Toolkit;

/**
 * 防御XSS
 * @author 云体检通用漏洞防护补丁v1.1
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月5日下午2:45:48
 */
final class Xss{
  use Injection;
  public static function defense(){
    $url_arr=[ 'xss'=>"\\=\\+\\/v(?:8|9|\\+|\\/)|\\%0acontent\\-(?:id|location|type|transfer\\-encoding)", ];
    
    $args_arr=[
                'xss'=>"[\\'\\\"\\;\\*\\<\\>].*\\bon[a-zA-Z]{3,15}[\\s\\r\\n\\v\\f]*\\=|\\b(?:expression)\\(|\\<script[\\s\\\\\\/]|\\<\\!\\[cdata\\[|\\b(?:eval|alert|prompt|msgbox)\\s*\\(|url\\((?:\\#|data|javascript)",
                'sql'=>"[^\\{\\s]{1}(\\s|\\b)+(?:select\\b|update\\b|insert(?:(\\/\\*.*?\\*\\/)|(\\s)|(\\+))+into\\b).+?(?:from\\b|set\\b)|[^\\{\\s]{1}(\\s|\\b)+(?:create|delete|drop|truncate|rename|desc)(?:(\\/\\*.*?\\*\\/)|(\\s)|(\\+))+(?:table\\b|from\\b|database\\b)|into(?:(\\/\\*.*?\\*\\/)|\\s|\\+)+(?:dump|out)file\\b|\\bsleep\\([\\s]*[\\d]+[\\s]*\\)|benchmark\\(([^\\,]*)\\,([^\\,]*)\\)|(?:declare|set|select)\\b.*@|union\\b.*(?:select|all)\\b|(?:select|update|insert|create|delete|drop|grant|truncate|rename|exec|desc|from|table|database|set|where)\\b.*(charset|ascii|bin|char|uncompress|concat|concat_ws|conv|export_set|hex|instr|left|load_file|locate|mid|sub|substring|oct|reverse|right|unhex)\\(|(?:master\\.\\.sysdatabases|msysaccessobjects|msysqueries|sysmodules|mysql\\.db|sys\\.database_name|information_schema\\.|sysobjects|sp_makewebtask|xp_cmdshell|sp_oamethod|sp_addextendedproc|sp_oacreate|xp_regread|sys\\.dbms_export_extension)",
                'other'=>"\\.\\.[\\\\\\/].*\\%00([^0-9a-fA-F]|$)|%00[\\'\\\"\\.]"
              ];
    
    $referer=empty($_SERVER['HTTP_REFERER']) ? [] : [$_SERVER['HTTP_REFERER']];
    $query_string=empty($_SERVER["QUERY_STRING"]) ? [] : [$_SERVER["QUERY_STRING"]];

    self::check_data($query_string,$url_arr);
    foreach([$_GET,$_POST,$_COOKIE,$referer] as $arr){
      self::check_data($arr,$args_arr);
    }
  }
  
  public static function check_data(array $arr,$v) {
    foreach($arr as $key=>$value){
      if(!is_array($key)){ 
        self::check($key,$v);
      }else{
        self::check_data($key,$v);
      }
  
      if(!is_array($value)){
        self::check($value,$v);
      }else{
        self::check_data($value,$v);
      }
    }
  }
  
  public static function check($str = '',array $v){
    foreach($v as $key=>$value){
      if (preg_match("/".$value."/is",$str)==1||preg_match("/".$value."/is",urlencode($str))==1){
        $msg = "IP:{ip}\n页面:{page}\n提交方式:{reqmethod}\n提交数据:{str}";
        global $CONFIGS;
        $CONFIGS->logger->alert($msg,['ip'=>$_SERVER['REMOTE_ADDR'],
                                    'page'=>'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
                                    'reqmethod'=>$_SERVER['REQUEST_METHOD'],
                                    'str'=>$str  ]);
        http_response_code(403);
        exit();
      }
    }
  }
}