<?php namespace es\core;
/**
 * 框架执行前与执行后运行
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月5日下午2:54:48
 */
class Hook{
/**
 * 控制器加载前执行
 */
  public function before_controller(){
    // 
    $this->sub_hook();
  }
  
/**
 * 控制器加载后，页面输出前执行
 */
  public function after_cintroller(){
    $this->sub_hook('after_cintroller');
    //$controller = Controller::get_instance();
  }
  
/**
 * 获取钩子的子类并执行子类方法
 * @param string $mehod 子类执行的方法
 */
  private function sub_hook($mehod='before_controller'){
    if( file_exists(APPPATH.'core/hook.php') ){
      $hook = new \app\core\Hook();
      if(is_subclass_of($hook,'\es\core\Hook')){
        $hook->{$mehod}();
      }
    }
  }
  
}