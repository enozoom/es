<?php defined('SYSPATH') OR exit('POWERED BY Enozoomstudio');
/**
 * 基类控制器
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2015年12月19日下午2:08:37
 * ------------------------------
 * 更改 _is_post()修饰符protected
 * 增加 cmdq() 获取当前控制器的文件夹，控制器名，控制器方法，控制器方法参数
 */
class ES_controller{
  private static $instance;
  public function __construct(){
    self::$instance =& $this;
    $this->load = new ES_load();
    $this->output = new ES_output();
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
      if($val instanceof ES_model ){
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
   * 默认方法
   */
  public function index(){
    echo 'Hi,ES!';
  }
  
  protected function _cmdq(){
    global $Route;
    return $Route->cmdq;
  }
}
