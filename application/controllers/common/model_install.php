<?php defined('APPPATH') OR exit('POWERED BY Enozoomstudio');
/**
* 
* @author Joe e@enozoom.com
* 2015年10月7日 下午3:34:40
*/
class Model_install extends ES_controller{
  public function index(){
    $this->load->model('install_model','i');
    $this->i->init('e_article');
  }
}