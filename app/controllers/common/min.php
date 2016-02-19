<?php
namespace app\controllers\common;
/**
* min控制中方法无法传递多组参数
* @author joe e@enozoom.com
* 
* 如 index.php?c=min&m=index&d=common&q=1,2,3.css
* function index($a,$b,$c){ }
* 
* 正常情况下会解析 $a=1;$b=2;$c=3
* 但min控制器解析  $a='1,2,3';$b=null;$c=null
* -------------------------------------------
* 升级
* 2015年2月13日
* ·对CSS合并文件进行去空格
* ---------------------
* 2015年5月23日10:51:10
* ·增加对不同文件夹的支持
* ·对文件进行gzip压缩
* ---------------------
* 2015年6月25日13:38:10
* ·增加对less支持
* ·加入缓存
* ---------------------
* 2015年8月7日14:19:57
* ·修复读取缓存时响应头信息错误
* ·增加全局属性$compress方便开启gzip压缩
* ---------------------
*  2015年9月13日15:30:46
* ·使用外部的configs/config.eno里面的theme_path来定义，css,js,less的存放路径
* ---------------------
* 2016年1月16日14:16:33
* ·区分出管理后台的css，js文件夹，不再放置在./theme下
*/
class min extends \es\core\Controller{
  private $cache = FALSE;
  private $compress = FALSE;
  private $cache_dir;
  private $cache_suffix = '.clej';
  private $def_dir;
  private $is_admin = FALSE;// 是否是后台样式
  public function __construct(){
    parent::__construct();
    $this->cache_dir = APPPATH.'cache/cssjsless/';
    $this->def_dir = './theme/'.$this->_configs('theme_path').'/';
    // 管理后台的css,js放置地址
    if( strpos($this->_cmdq()['q'], 'esadmin-')!==FALSE ){
      $this->is_admin = TRUE;
      $this->def_dir = APPPATH.'data/esadmin/';
    }
  }
/**
 * 入口
 * @param string $files 文件名字符串+文件类型后缀，如base,dom.css。多个文件以","分割，以最终后缀决定最后的输出
 */
  public function index($files=''){
    $this->is_admin && $files = str_replace('esadmin-', '', $files);
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
 * 合并后的文件进行css输出，输出前，将less解析为css
 * @param string $str
 * @return mixed
 */  
  private function _less($str=''){
    $this->load->library('Less/Less_Parser','less');
    $this->less->parse($str);
    return $this->_css($this->less->getCss());
  }

/**
 * 压缩字符串
 * @param string $str
 * @return void 直接输出到页面
 */  
  private function compress($str,$suffix='css'){
    switch ($suffix){
      case 'css':case 'less':
        header('Content-type:text/css');
      break;case 'js':
        header('Content-type:application/x-javascript');
      break;
    }
    
    $this->compress && extension_loaded('zlib') && ob_start('ob_gzhandler');
    echo $str;
    $this->compress && extension_loaded('zlib') && ob_end_flush();
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