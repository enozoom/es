<?php namespace app\core;
/**
 * 钩子
 * 重写父类的方法时,如before_controller，无需声明parent::before_controller;否则会死循环。
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月9日下午3:43:28
 */
class Hook extends \es\core\Hook{
  public function before_controller(){
    //echo 'subb before';
  }
  public function after_cintroller(){
    //echo 'subb after';
  }
}
