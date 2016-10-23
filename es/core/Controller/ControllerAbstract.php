<?php
namespace es\core\Controller;
use es\core\Load\Load;
use es\core\Toolkit\Output;
use es\core\Toolkit\ConfigTrait;

abstract class ControllerAbstract{
  use ConfigTrait;
  
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
      if($val instanceof \es\core\Model\ModelAbstract){
        $this->$var->db->close();
        break;
      }
    }
  }
}