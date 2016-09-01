<?php
/**
 * 单纯的数据操作控制器
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月22日上午9:46:38
 */
namespace es\core\Controller;

use es\core\Http\Response;
class DataController extends AbstractController{
  use Response{ render as private oRender; }
  
  protected function render($str='',$type='html',$httpCache=24){
    $this->oRender($str,$type,$httpCache);
    $this->closeDB();
    exit();
  }
}