<?php
namespace app\controllers\common;
/**
* 
* @author Joe e@enozoom.com
* 2015年10月7日 下午3:34:40
*/
class Install extends \es\core\Controller{
  public function model(){
    $this->load->model('install','i');
    $this->i->init();
  }
}