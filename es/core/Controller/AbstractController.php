<?php
namespace es\core\Controller;
use es\core\Load\Load;
use es\core\Toolkit\Output;
use es\core\Toolkit\Config;

abstract class AbstractController{
  use Config;
  
  private static $instance;
  public $load;
  public $output;
  
  public function __construct(){
    self::$instance =& $this;
    $this->load = new Load();
    $this->output = new Output();
    isset($_SESSION) || session_start();
  }
  
  public static function &getInstance(){
    return self::$instance;
  }
  
  public function closeDB(){
    foreach( get_object_vars($this) as $var=>$val ){
      if($val instanceof \es\core\Model\Model){
        $this->$var->db->close();
        break;
      }
    }
  }
}