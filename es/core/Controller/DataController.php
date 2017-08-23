<?php
/**
 * 单纯的数据操作控制器
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月22日上午9:46:38
 */
namespace es\core\Controller;

use es\core\Http\ResponseTrait;
use es\core\Toolkit\AryTrait;
class DataController extends ControllerAbstract{
  use AryTrait,ResponseTrait{ render as private oRender; }
  
  protected function render($str='',$type='html',$httpCache=24){
    $this->oRender($str,$type,$httpCache);
    $this->closeDB();
    exit();
  }
  
  /**
   * 获取POST提交数据且必须包含$requires中存在的数据
   * @param array $requires
   * @return bool
   */
  protected function __postRequires($requires=[]){
      return strtolower($_SERVER['REQUEST_METHOD'])=='post' && $this->isRequired($requires,$_POST);
  }
  
  /**
   * logger->debug的别名
   * @param string $msg
   */
  protected function __log($msg){
      $this->getConfigs('logger')->debug($msg);
  }
}