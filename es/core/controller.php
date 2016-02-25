<?php
namespace es\core;
/**
 * 基类控制器
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2015年12月19日下午2:08:37
 * ------------------------------
 * 更改 _is_post()修饰符protected
 * 增加 cmdq() 获取当前控制器的文件夹，控制器名，控制器方法，控制器方法参数
 */
class Controller{
  private static $instance;
  public function __construct(){
    self::$instance =& $this;
    $this->load = new Load();
    $this->output = new Output();
    isset($_SESSION) || session_start();
  }
  
  /**
   * 静态化控制器
   */
  public static function &get_instance(){
    return self::$instance;
  }
  
  /**
   * 关闭数据库连接
   */
  public function closeDB(){
    foreach( get_object_vars($this) as $var=>$val ){
      if($val instanceof Model){
        $this->$var->db->close();    
      }
    }
  }
  /**
   * 是否为POST提交,所有的数据修改均需要POST提交.
   * @return bool
   */
  protected function _is_post(){
    return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
  }
  
  /**
   * 检查数据数组中必须包含的值
   * @param array $requires 必须的值的键数组
   * @param array $data     要查询数组
   * @return bool 全部有值则为true
   */
  protected function _is_require($requires=[],$data=[]){
    empty($data) && $data = $_GET;
    foreach($requires as $k){
      if(empty($data[$k])) return FALSE;
    }
    return TRUE;
  }
  
  /**
   * 默认方法
   */
  public function index(){
    echo 'Hi,ES!';
  }
  
/**
 * 当前控制器的文件夹，方法及参数
 */
  protected function _cmdq(){
    global $Route;
    return $Route->cmdq;
  }
  
/**
 * 获取配置文件中的参数
 * @param string $config_file 指定配置文件  config|contants|database|routes
 * @param string $key 配置文件中的参数
 * @return mix obj|string|''
 */
  protected function _configs($key='',$config_file='config'){
    global $configs;
    if(empty($config_file)) return $configs;
    if(empty($key) && !empty($configs->$config_file)) return $configs->$config_file;
    return empty($configs->$config_file->$key)?'':$configs->$config_file->$key;
  }
}
