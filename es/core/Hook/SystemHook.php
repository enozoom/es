<?php
namespace es\core\Hook;

class SystemHook extends AbstractHook{
/**
 * 框架加载前
 * {@inheritDoc}
 * @see \es\core\Hook\AbstractHook::before()
 */
  public function before(){
    // 版本检查
    if(!(version_compare(PHP_VERSION, '5.5.0', '>') && version_compare(PHP_VERSION, '7.0.0', '<'))){
      die('Requires PHP version between 5.5.x and 5.6.x, Your version: ' . PHP_VERSION );
    }
    // 防御XSS攻击
    \es\core\Toolkit\Xss::defense();
  }
  public function after(){
    
  }
}