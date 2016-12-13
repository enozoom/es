<?php namespace es\core\Hook;
/**
 * 框架执行前与执行后运行
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月5日下午2:54:48
 */
abstract class HookAbstract{
  /**
   * 控制器未初始化前
   */
  public abstract function beforeController();
  /**
   * 控制器实例化成功后
   */
  public abstract function afterController();
  /**
   * 控制器实例化成功后调用响应方法
   */
  public abstract function afterControllerMethod();
  
/**
 * 获取钩子的子类并执行子类方法
 * @param string $mehod 子类执行的方法
 */
  protected function sub_hook($mehod='before_controller'){
    if( file_exists(APPPATH.'core/hook.php') ){
      $hook = new \app\core\Hook();
      if(is_subclass_of($hook,'\es\core\Hook')){
        $hook->{$mehod}();
      }
    }
  }
  
}