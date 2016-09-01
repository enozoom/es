<?php
namespace app\controllers\common;

use es\core\Controller\DataController;

final class min extends DataController{
  private $cache = FALSE;
  private $cache_dir;
  private $cache_suffix = '.clej';
  private $def_dir;
  private $is_admin = FALSE;// 是否是后台样式
  public function __construct(){
    parent::__construct();
    $this->cache_dir = APPPATH.'cache/cssjsless/';
    $this->def_dir = './theme/'.$this->getConfig('theme_path').'/';
    // 非前台地址
    if(preg_match('/([^-]+)-/',$this->getConfigs('cmdq')->q,$matches)){
      if(is_dir($dir = APPPATH.'data/'.$matches[1])){
        $this->def_dir = $dir.'/';
      }
    }
  }
/**
 * 入口
 * @param string $files 文件名字符串+文件类型后缀，如base,dom.css。多个文件以","分割，以最终后缀决定最后的输出
 */
  public function index($files=''){
    $this->is_admin && $files = str_replace('esadmin-', '', $files);
    if($i = strpos($files, '-')) $files = substr($files, $i+1);
    $output = $this->cache($files);
    $suffix = substr($files,strrpos($files,'.')+1);// 获取后缀
    empty($output) || $this->compress($output,$suffix);
  }
  
/**
 * 对合并文件进行css输出
 * @param string $str
 * @return string
 */    
  private function _css($str=''){
    $str = preg_replace(array('/{\s*([^}]*)\s*}/','/\s*:\s*/','~\/\*[^\*\/]*\*\/~s'),array('{$1}',':',''),$str);
    $str = preg_replace(array('/'.PHP_EOL.'/','/\n*/'),'',$str);
    return $str;
  }
  
/**
 * 对合并文件进行js输出
 * @param string $str
 * @return string
 */  
  private function _js($str=''){
    return $str;
  }

/**
 * 压缩字符串
 * @param string $str
 * @return void 直接输出到页面
 */  
  private function compress($str,$suffix='css'){
    $this->render($str,$suffix);
  }

/**
 * 读取或将合并文件包含字符写入缓存
 * @param string $files
 * @return string
 */  
  private function cache($files=''){
    $output = '';
    if($this->cache){// 从缓存中读取
      $filename = sha1($files).$this->cache_suffix;
      $path = './'.$this->cache_dir.$filename;
      file_exists($path) && $output = file_get_contents($path);
    }    
    $filetype = substr($files,strrpos($files,'.')+1);// 获取后缀
    
    if(empty($output)){// 从文件中读取
      $files = str_replace(".{$filetype}",'',$files);// 去后缀
      
      foreach(explode(',',$files) as $f){
        $path = $this->def_dir.$filetype."/{$f}.".$filetype;
        if(file_exists($path)){
          $output .= file_get_contents($path);
        }
      }
      if(!empty($output)){
        $method = "_{$filetype}";
        $output = $this->$method($output);
        $this->cache &&
        file_put_contents($this->cache_dir.$filename, $output);
      }      
    }
    
    return $output;
  }
}