<?php defined('SYSPATH') OR exit('POWERED BY Enozoomstudio');
/**
* -----------------------------------------
* 对uri进行解析
* 实例化ES_Controller
* 执行指定的方法并传入方法所需的值 
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
* -----------------------------------------
* cmdq = controller-mothod-directory-query
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
    
    $query = $_SERVER['QUERY_STRING'];
    isset($_SERVER['SCRIPT_URL']) && $query = $_SERVER['SCRIPT_URL'];//SAE
    
    empty($query) && $query = $_SERVER['REQUEST_URI'];
    
    if( strlen($query)==1 || strpos($query,'/') === FALSE ){
      
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
    }else{// 可能设置的是静态路径
      $query = $this->static_url($query);
    }
    
    $this->cmdq = $this->preg_controller($query);
    $this->cls_instance($this->cmdq);
  }
  
/**
  * URL被设置为伪静态化
  * 如果设置伪路径，建议使用 /文件夹/控制器/方法/参数 顺序配置。
  * 
  * 将URL正确的解析成 c=&m=&d=&q= 字符串
  * @param string $query
  * @return
  */
  public function static_url($query){
    global $configs;
    $suffix = $configs->config->suffix;
    // 不需要去的后缀
    $mimes = array('css','js','less');
    if(!empty($suffix)){
      $mimes[] = $suffix;
      // 去后缀
      $query = str_replace('.'.$suffix,'',$query);
    }

    // 对url进行兼容处理
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
      $preg = "#{$route->pattern}#";
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
        $args = explode( '/',"public/{$args[0]}/index/" );
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
    $cmdq = array('c'=>'index','m'=>'index','d'=>'public','q'=>'');
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
  private function cls_instance($cmd){
    $dir = $this->dir_controller.$cmd['d'];
    is_dir($dir) || show_500('控制器文件路径不正确'.$dir);
    $ctl_cls = $cmd['c']; 
    
    require SYSPATH.'core/controller.php';
    // 拓展基本控制器基类
    $dir_controller_ext = APPPATH.'core/e_controller.php';
    if(file_exists($dir_controller_ext)){
      require $dir_controller_ext;
    }
    
    // 各文件夹自定义控制器基类
    $dir_controller = "{$dir}/e_{$cmd['d']}.php";
    if(file_exists($dir_controller)){
      require $dir_controller;
    }
    
    $dir = "$dir/{$ctl_cls}.php";
    is_file($dir) || show_500('控制器文件不存在'.$dir);
    require $dir;
    $ctl_cls = ucfirst($ctl_cls);     
    
    // 增加命名空间的支持2015年9月9日23:25:15
    try{
      $reflector = new ReflectionClass(ucfirst($cmd['d']).'\\'.$ctl_cls);
    }catch (Exception $e){
      $reflector = new ReflectionClass($ctl_cls);
    }
        
    $rMethod = null;
    try{
      $rMethod = $reflector->getMethod($cmd['m']);
    }catch(Exception $e){
      show_500("控制器{$cmd['c']}方法{$cmd['m']}不存在");
    }
    $this->disable_method( $rMethod );
    
    $rMethod->isConstructor() && show_500('用默认函数的类需要有构造函数__construct()');
    $args = array();
    if(!empty($cmd['q'])){// 如果使用汇总压缩css,js,则不能用,分割参数
      $args = $cmd['c'] == 'min' ? array($cmd['q']) : explode(',',$cmd['q']);
    }

    $cls = $reflector->newInstance();
    
    is_subclass_of($cls,'ES_controller') || show_500('控制器必须是ES_controller的子类');
    $rMethod->invokeArgs($cls,$args);
    $cls->closeDB();// 关闭数据库
    
    $flag = $cls->load->config('config','compress_outpage');
    $flag && $cls->output->length() > 1024 && extension_loaded('zlib') && ob_start('ob_gzhandler');
    $cls->output->display();// 输出
    $flag && $cls->output->length() > 1024 && extension_loaded('zlib') && ob_end_flush();    
   }
   
/**
 * 重写$_GET
 */
  private function rewrite_GET(){
    $uri = $_SERVER['REQUEST_URI'];
    
    isset($_SERVER['HTTP_X_ORIGINAL_URL']) && 
    $uri = $_SERVER['HTTP_X_ORIGINAL_URL'];
    
    if( ($idx = strpos($uri,'?')) && strpos($uri,'=') ){// $_GET有参值
      $_get = array();
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
  private function disable_method( ReflectionMethod $rMethod ){
    if(!$rMethod->isPublic() || strpos($rMethod->name, '_') === 0 ) show_404();
  }
}