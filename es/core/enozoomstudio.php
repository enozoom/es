<?php 
/**
 * ES4.0160621
 * @author Joe e@enozoom.com
 * 2016年6月24日09:44:36
 */
namespace es\core;
use es\core\Load\ConfigStatic;
use es\core\Hook\SystemHook;
use es\core\Http\Cmdq;
use es\core\Route\Route;
use es\core\Log\Logger;

// 自动加载
spl_autoload_register(function($classname){
  if( strpos($classname,'\\') ){
    $_path = str_replace('\\', '/', $classname).'.php';
    if( file_exists($_path) ){
      require $_path;
      return '';
    }
  }
});

// 配置文件
$CONFIGS = ConfigStatic::init();
// 日志
$CONFIGS->logger = Logger::getInstance();

// 是否开启调试
error_reporting($CONFIGS->config->debug?E_ALL:0);

// 系统前加载
$CONFIGS->hook = new SystemHook;
$CONFIGS->hook->before();

// 请求
$CONFIGS->cmdq = (new Cmdq())->get();

// 路由
// 系统加载完成后执行
(new Route())->initController();