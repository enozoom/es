<?php defined('SYSPATH') OR exit('POWERED BY Enozoomstudio');
require SYSPATH.'core/load.php';
require SYSPATH.'core/output.php';
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
  public function _is_post(){
    return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
  }
  /**
   * 默认方法
   */
  public function index(){
    echo 'Hi,ES!';
  }
}
?>