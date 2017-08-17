<?php
/**
 * 缓存操作
 * @author Joe e@enozoom.com
 * 2015年10月13日 下午1:09:12
 */
namespace es\core\Cache;

use es\core\Toolkit\ConfigTrait;
use es\core\Toolkit\FileStatic;
use es\core\Toolkit\StrStatic;
use es\core\Toolkit\TimeStatic;
use es\core\Http\HeaderTrait;

class Cache{
  use ConfigTrait,HeaderTrait;
  /**
   * 打开缓存功能
   * @var bool
   */
  public $open = FALSE;
  
  /**
   * 是否打开压缩
   * @var bool
   */
  public $compress = TRUE;
  
  /**
   * 缓存的路径
   * @var string
   */
  protected $cache_path = 'html';
  
  /**
   * 缓存的文件后缀
   * 也很会影响header的响应结果
   * @var string
   */
  protected $cache_file_suffix = '.html';
  
  /**
   * 缓存过期时间，超过本时间会重新获取最新数据
   * 1天 = 86400
   * 1周 = 604800
   * @var int
   */
  protected $expires = 604800;
  
  /**
   * 允许静态缓存的控制器下的方法,
   * c=>'*',表示该控制器下所有方法均使用缓存
   * @var array(d1=>array('c1'=>'m1,m2'),d2=>array('c2'=>'*'))
   */
  protected $cache_controller_method = [];
  
  /**
   * array(c=>控制器,m=>控制器方法,d=>控制器文件夹,q=>控制器方法参数)
   * @var array
   */
  protected $cmdq = null;
  
  /**
   * 初始化
   * @param array $cmdq array(c=>控制器,m=>控制器方法,d=>控制器文件夹,q=>控制器方法参数)
  */
  public function __construct(){
    $configs = $this->getConfigs('config');
    // 是否开启缓存
    $this->open = $configs->cache;
    // 可以被缓存的文件
    empty($configs->cache_allow) ||
    $this->cache_controller_method = json_decode(json_encode($configs->cache_allow),1);
    // 加入城市站
    $this->cache_path .= '/'.$this->getConfig('dbname','database');
    // 是否压缩输出
    $this->compress = $configs->compress_outpage;
    // cmdq
    $this->cmdq = $this->getConfigs('cmdq');
  }
  
  /**
   * 保存缓存
   * 如果页面内容小于1KB，则不被缓存即使满足缓存条件
   * @param string $html
   * @param int $len 文件最小存储限制
   */
  public function save($html,$len=1024){
    if($this->is_allow_cache() && mb_strlen($html,'UTF-8')>$len){
      $file = $this->cache_file_path();
      $html = StrStatic::cleanHtmlblank($html);
      $es = PHP_EOL."<!--[[".ES_POWER.' '.ES_AUTHOR.' '.
                   TimeStatic::formatTime().' '.
                   $this->cache_file_rule().
                   time()."]]-->";// 该值用以判断是否过期
      file_put_contents($file, $html.$es);
    }
  }
  
  /**
   * 读取缓存
   * @param bool $print 是否直接打印
   * @return void|string
   */
  public function read($print=TRUE){
    if($this->is_allow_cache()){
      $file = $this->cache_file_path();
      if(file_exists($file)){
        $html = file_get_contents($file);
        $time = str_replace(']]-->','',mb_substr($html,-15,NULL,'UTF-8'));
        is_numeric($time) || die('缓存文件格式不正确，无法正确读取');
        if( time() - $time < $this->expires ){
          if(!$print) return $html;
          $this->httpMime( substr($this->cache_file_suffix,1) );
          $this->compress && extension_loaded('zlib') && ob_start('ob_gzhandler');
          echo $html;
          $this->compress && extension_loaded('zlib') && ob_end_flush();
          exit();// 执行全部结束
        }
      }
    }
    if(!$print) return '';
  }

  /**
   * 清理缓存
   * @param string $uri
   * @return bool 是否删除成功
   */
  public function clean($uri = ''){
      empty($uri) && $uri = $this->cache_file_path();
      //$this->getConfigs('logger')->debug($uri);
      return file_exists($uri)?unlink($uri):TRUE;
  }
  
  /**
   * 重命名缓存文件夹从而达到清缓的目的
   * 因为文件仍然存在，积累多了占用磁盘空间。
   * @return bool
   */
  public function renameCacheDir($dir=''){
      empty($dir) && $dir = $this->cache_dir_rule();
      if( substr($dir, -1)=='/' ){
          $dir = substr($dir,0,-1);
      }
      return rename($dir,$dir.date('YmdHis'));
  }
  
  /**
   * 清除所有的缓存备份文件夹
   * 方法耗时且占用磁盘读写空间，建议在访问量少的时间进行清理。
   * @param string $beforeDate 清理$beforeDate之前的缓存备份 $beforeDate = 20161008101010
   * @param string $dir 哪个缓存文件夹下
   * @return bool 除非执行失败，正常情况均返回true
   */
  public function delCacheBKDir($beforeDate='',$dir=''){
      empty($beforeDate) && $beforeDate = date('YmdHis');
      $dir = APPPATH.'cache'.(empty($dir)?'':'/'.$dir);
      $files = [];
      $this->scanDir($files,APPPATH.'cache',function($d){
          return strlen($d)>14 && is_numeric( substr($d, -14) );
      });
      
      foreach($files as $k=>$v){
          if( $beforeDate > substr($k,-14) ){
              $this->delDir($v);
          }
      }
      return true;
  }
  
  /**
   * 缓存文件夹结构规则
   * @param string $uri
   * @return string
   */
  protected function cache_dir_rule(){
    return FileStatic::mkdir(APPPATH."cache/{$this->cache_path}/{$this->cmdq->d}/{$this->cmdq->c}");
  }
  
  /**
   * 缓存文件命名规则（文件+后缀）
   * @param string $uri
   * @return string
   */
  protected function cache_file_rule(){
    $get = '';
//  2016年8月30日11:23:55，不再支持含get参数的缓存，因其造成了整个页面的缓存点全部再次缓存的问题。
//     if(!empty($_GET)){
//       ksort($_GET);
//       $get = '.'.sha1( http_build_query( $_GET ) );
//     }
    //$this->getConfigs('logger')->debug("{$this->cmdq->m}_{$this->cmdq->q}");
    return sha1("{$this->cmdq->m}_{$this->cmdq->q}").$get.$this->cache_file_suffix;
  }
  
  /**
   * 缓存文件路径（文件夹+文件）
   * @param string $uri
   * @return string
   */
  protected function cache_file_path($uri=''){
    $dir = $this->cache_dir_rule();
    $file = $this->cache_file_rule();
    return $dir.$file;
  }
  
  /**
   * 是否允许缓存
   * 依次判断控制器文件夹，控制器，控制器方法
   * @return boolean
   */
  protected function is_allow_cache(){
    $flag = FALSE;
    if($this->open){
      foreach( $this->cache_controller_method as $d=>$cm ){
        if( $d == $this->cmdq->d ){
          foreach($cm as $c=>$m){
            if( $c == $this->cmdq->c ){
              if( $m == '*' || in_array($this->cmdq->m, explode(',', $m) ) ){
               $flag = TRUE;
              }
              break;
            }
          }
        }
      }
    }
    return $flag;
  }


}