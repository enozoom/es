<?php
namespace es\core;
/**
* -----------------------------------------
* 对uri进行解析
* 实例化ES_Controller
* 执行指定的方法并传入方法所需的值 
* cmdq = controller-mothod-directory-query
* -----------------------------------------
* 2014年10月9日16:28:48
* | 支持伪静态
* | 重新设置$_GET,以支持伪静态
* 2015年1月15日14:37:10
* | 支持MIN-CSS-JS的合并
* 2015年5月23日10:08:59
* | 支持MIN-CSS-JS的非public文件夹合并
* 2015年5月23日14:05:17
* | 支持gizp压缩输出 
* 2015年9月9日23:25:40
* | 增加控制器对命名空间的支持
* | 命名空间第一个字母要大写并且与文件夹名一致
* 2015年9月20日20:43:02
* | 国内空间中的PHP版本大多是5.2，含泪移除命名空间
* 2015年10月9日13:44:04
* | 增加对控制器方法的访问限制
* | 修饰符非public,或者方法名以_开头均不予前台显示
* 2015年10月13日09:27:14
* | 对以‘/’结尾和非‘/’结尾的url兼容
* 2015年10月13日13:50:46
* | 增加对缓存的支持
* 2016年2月17日13:35:56
* | 对configs/routes.eno进行全字符串匹配
* 2016年2月19日12:21:52
* | 自动为无后缀控制器方法访问末尾加'/'
* -----------------------------------------
*
*/
class Route{
  private $routes;
  private $dir_controller;
  private $default_method = 'index';
  public $cmdq;
  
  public function __construct($__routes){
    $this->routes = $__routes;
    $this->dir_controller = APPPATH.'controllers/';
  }
  
/**
  * 将路由解析
  * 并加载相关类文件，调用相关方法
  * 
  * @return void
  */
  public function resolve(){
    $this->rewrite_GET();
    
    $query = '';
    isset($_SERVER['QUERY_STRING']) && $query = $_SERVER['QUERY_STRING'];
    isset($_SERVER['SCRIPT_URL']) && $query = $_SERVER['SCRIPT_URL'];//SAE
    empty($query) && $query = $_SERVER['REQUEST_URI'];
    
    // 解析出 cmdq格式的参数url字符串，并对应$this->cmdq
    if( strlen($query)==1 || strpos($query,'/') === FALSE ){
      $query = $this->dynamic_url($query);
    }else{// 可能设置的是静态路径
      $query = $this->static_url($query);
    }
    $this->cmdq = $this->preg_controller($query);
    
    // 实例化控制器
    $this->cls_instance();
  }
  
/**
  * URL是一个动态地址
  * 将URL正确的解析成 c=&m=&d=&q= 字符串
  * @param string $query
  * @return string c=&m=&d=&q=
  */
  private function dynamic_url($query){
    if(!isset($_GET['c']) && !isset($_GET['m']) && !isset($_GET['d'])){
      foreach($this->routes as $route){
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
    return $query;
  }
  
  
/**
  * URL被设置为伪静态化
  * 如果设置伪路径，建议使用 /文件夹/控制器/方法/参数 顺序配置。
  * 
  * 将URL正确的解析成 c=&m=&d=&q= 字符串
  * @param string $query
  * @return string c=&m=&d=&q=
  */
  private function static_url($query){
    global $configs;
    $suffix = $configs->config->suffix;
    // 不需要去的后缀
    $mimes = array('css','js','less');
    if(!empty($suffix)){
      $mimes[] = $suffix;
      // 去后缀
      $query = str_replace('.'.$suffix,'',$query);
    }

    // 如果第一个字符是/，去掉
    substr($query,0,1) === '/' && $query = substr($query,1);
    // 如果最后一个字符是/， 去掉
    substr($query,-1,1) === '/' && $query = substr($query,0,strlen($query)-1);
    // 分割参数
    $args = explode('/',$query);
    
    // 如果未使用配置中的后缀，则404
    if( ($i=strrpos( $last_segement=$args[count($args)-1],'.') )>0){
      in_array(substr($last_segement,$i+1),$mimes) ||  show_404();
    }
    
    $routes = $this->routes;
    
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
    show_404();
  }
  
  
/**
  * 判断准确的controller
  * @param string $str 需要解析的字符串 
  * 格式：c=控制器名&m=控制器方法名&d=控制器文件夹&q=参数1，参数2
  * 
  * @return
  */  
  private function preg_controller($str){
    $cmdq = array('c'=>'index','m'=>'index','d'=>'esweb','q'=>'');
    $str = str_replace('amp;','',$str);
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
  }
  
/**
* controller的实例化
*  
* @param array $cmd
* $cmd = array('c'=>'index','m'=>'index','d'=>'public','q'=>'a,b,c');
* @return
*/  
  private function cls_instance(){
    // 开始缓存
    $cache = new Cache($this->cmdq);
    $cache->read();
    $dir = $this->dir_controller.$this->cmdq['d'];
    is_dir($dir) || show_500('控制器文件路径不正确'.$dir);
    
    $ctl_cls = ucfirst($this->cmdq['c']);
    
    try{
      $reflector = new \ReflectionClass(sprintf('\\app\\controllers\\%s\\%s',$this->cmdq['d'],$ctl_cls));
    }catch (\Exception $e){
      var_dump($e);
      show_500("无法通过反射，获取类{$ctl_cls}！");
    }
        
    $rMethod = null;
    try{
      $rMethod = $reflector->getMethod($this->cmdq['m']);
    }catch(\Exception $e){
      show_500("控制器{$this->cmdq['c']}方法{$this->cmdq['m']}不存在");
    }
    $this->disable_method( $rMethod );
    
    $rMethod->isConstructor() && show_500('用默认函数的类需要有构造函数__construct()');
    $args = [];
    if(!empty($this->cmdq['q'])){// 如果使用汇总压缩css,js,则不能用,分割参数
      $args = $this->cmdq['c'] == 'min' ? array($this->cmdq['q']) : explode(',',$this->cmdq['q']);
    }

    $cls = $reflector->newInstance();
    
    is_subclass_of($cls,'\es\core\Controller') || show_500('控制器必须是\es\core\Controller的子类');
    $rMethod->invokeArgs($cls,$args);
    $cls->closeDB();// 关闭数据库

    $html = $cls->output->display(1);// 输出
    $cache->save($html);
    
    $flag = $cls->load->config('config','compress_outpage');
    $flag && $cls->output->length() > 1024 && extension_loaded('zlib') && ob_start('ob_gzhandler');
      echo $html;
    $flag && $cls->output->length() > 1024 && extension_loaded('zlib') && ob_end_flush();
  }
   
/**
 * 重写$_GET
 */
  private function rewrite_GET(){
    $uri = $_SERVER['REQUEST_URI'];
    isset($_SERVER['HTTP_X_ORIGINAL_URL']) && $uri = $_SERVER['HTTP_X_ORIGINAL_URL'];
    
    if(!strrchr($uri, '.') && substr($uri,-1)!='/' ){// 无后缀
      redirect_url( base_url($uri.'/') );
    }
    
    if( ($idx = strpos($uri,'?')) && strpos($uri,'=') ){// $_GET有参值
      $_get = [];
      $uri = substr( str_replace('amp','',$uri) ,$idx+1 );
        
      foreach( explode('&',$uri) as $kv ){
        $_kv = explode('=',$kv);
        $_get[$_kv[0]] = $_kv[1];
      }
    }
    $_GET = empty($_get)?null:$_get;
  }

/**
 * 禁止前台直接访问的方法们
 * @param ReflectionMethod $rMethod
 */
  private function disable_method( \ReflectionMethod $rMethod ){
    if(!$rMethod->isPublic() || strpos($rMethod->name, '_') === 0 ) show_404();
  }
}