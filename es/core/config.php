<?php 
namespace es\core;
/**
 * 读取配置文件参数
 * @author Joe e@enozoom.com
 * 2015年6月24日下午4:08:32
 * -----------------------
 * #2016年2月19日09:53:28 
 * 修正读取时字符串空格丢失的问题
 */

final class Config{
  static $configs;

/**
 * 进行json字符串标准化,并解析json后返回对象
 * @param string $filepath 配置文件路径
 * @return boolean|object 解析是失败返回NULL并有错误记录,配置文件为空则返回FALSE,正确解析返回Obj
 */  
  final static function readjsonstring($filepath){
    $jsonstring = file_get_contents($filepath);
    if(empty($jsonstring))return FALSE;
    
    //[去注释,去换行,单对象去多余的',',多对象去多余',']
    $pattern = ['~#.*\s*~','~\s*\n~','~,\n*}~','~,]~'];
    $replacement = ['','','}',']'];
    $jsonstring = preg_replace($pattern, $replacement, $jsonstring);
    
    $json = json_decode($jsonstring);
    empty($json)&&log_msg($filepath.PHP_EOL.$jsonstring,'配置文件解析失败，请检查文件内容');
    return $json;
  }
  
/**
 * 读取配置文件
 * @return obj
 */
  final static function readdir(){
    $dir = './configs';
    $suffix = '.eno';
    $configs = new \stdClass();
    foreach(scandir($dir) as $config){
      if(stripos($config, $suffix)!==FALSE){
        $k = str_ireplace($suffix, '', $config);
        $v = self::readjsonstring("{$dir}/{$config}");
        if($k=='constants'){// 常量们
          foreach($v as $kk=>$vv) define($kk, $vv);
        }else{
          empty($v) || $configs->$k = $v;
        }
      }
    }
    return $configs;
  }
  
  final static function init(){
    empty(self::$configs) && self::$configs = self::readdir();
    return self::$configs;
  }
}