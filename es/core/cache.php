<?php
namespace es\core;
/**
* 缓存操作
* @author Joe e@enozoom.com
* 2015年10月13日 下午1:09:12
*/
class Cache{
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
  protected $cmdq = [];
  
  /**
   * 初始化
   * @param array $cmdq array(c=>控制器,m=>控制器方法,d=>控制器文件夹,q=>控制器方法参数)
  */
  public function __construct($cmdq=[]){
   
    global $configs;
    // 是否开启缓存
    $this->open = $configs->config->cache;
    // 可以被缓存的文件
    empty($configs->config->cache_allow) ||
    $this->cache_controller_method = json_decode(json_encode($configs->config->cache_allow),1);
    // 是否压缩输出
    $this->compress = $configs->config->compress_outpage;
    $this->cmdq = $cmdq;
  }
  
  /**
   * 保存缓存
   * 如果页面内容小于1KB，则不被缓存即使满足缓存条件
   * @param string $html
   */
  public function save($html){
    if($this->is_allow_cache() && mb_strlen($html,'UTF-8')>1024){
      $file = $this->cache_file_path();
      $html = clean_htmlblank($html);
      $es = PHP_EOL."<!--[[".ENO_POWER.' '.ENO_AUTHOR.' '.
                   get_format_time().' '.
                   $this->cache_file_rule().
                   time()."]]-->";// 该值用以判断是否过期
      file_put_contents($file, $html.$es);
    }
  }
  
  /**
   * 读取缓存
   */
  public function read(){
    if($this->is_allow_cache()){
      $file = $this->cache_file_path();
      if(file_exists($file)){
        $html = file_get_contents($file);
        $time = str_replace(']]-->','',mb_substr($html,-15,NULL,'UTF-8'));
        is_numeric($time) || show_500('缓存文件格式不正确，无法正确读取');
        if( time() - $time > $this->expires ){
          $this->compress && extension_loaded('zlib') && ob_start('ob_gzhandler');
          echo $html;
          $this->compress && extension_loaded('zlib') && ob_end_flush();
          exit();// 执行全部结束
        }
      }
    }
  }

  /**
   * 清理缓存
   * @param string $uri
   * @return bool 是否删除成功
   */
  public function clean($uri = '/'){}
  
  /**
   * 清理整个文件夹
   * @param string $dir 要清理的文件夹
   * 不包括$this->cache_path中已包含的部分,如'article'
   * @return bool
   */
  public function clean_dir($dir){}
  
  /**
   * 缓存文件夹结构规则
   * @param string $uri
   * @return string
   */
  protected function cache_dir_rule(){
    return mk_dir(APPPATH."cache/{$this->cache_path}/{$this->cmdq['d']}/{$this->cmdq['c']}");
  }
  
  /**
   * 缓存文件命名规则（文件+后缀）
   * @param string $uri
   * @return string
   */
  protected function cache_file_rule($uri=''){
    return sha1("{$this->cmdq['m']}_{$this->cmdq['q']}").$this->cache_file_suffix;
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
  private function is_allow_cache(){
    $flag = FALSE;
    if($this->open){
      foreach( $this->cache_controller_method as $d=>$cm ){
        if( $d == $this->cmdq['d'] ){
          foreach($cm as $c=>$m){
            if( $c == $this->cmdq['c'] ){
              if( $m == '*' || in_array($this->cmdq['m'], explode(',', $m) ) ){
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