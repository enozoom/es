<?php
namespace es\core\Hook;

use es\core\Controller\ControllerAbstract;
class SystemHook extends HookAbstract{

  public function beforeController()
  {
    // 版本检查
    if(!(version_compare(PHP_VERSION, '5.5.0', '>') )){
      die('Requires PHP version >= 5.5.x, Your version: ' . PHP_VERSION );
    }
    // 防御XSS攻击
    \es\core\Toolkit\XssStaitc::defense();
  }
  
  public function afterController()
  {
      $controller = ControllerAbstract::getInstance();
  }
  
  public function afterControllerMethod()
  {
  }
}