<?php 
namespace es\core;
/**
 * 版本说明
 * ------------
 * V0.1100824
 * V1.0121006
 * V1.1130829
 * ------------
 * V2.0140624
 * 框架形成
 * ------------
 * V2.0150606
 * 类似CI的模式，在view调用上更加便捷
 * 支持不同数据库的数据切换
 * ------------
 * V2.1150624
 * 增加：namespace，less，haml的解析支持，让框架更关注业务逻辑，不再关注前端
 * ------------
 * V2.1151013
 * 并统一部分命名
 * 增加对缓存的支持
 * 增加微信的开发包system/libraries/api_wechat
 * ------------
 * V2.2160112
 * 使用命名空间，不再支持PHP5.3以下版本
 */

// 计时器
$_es_timer_begin = microtime(1);

// 通用函数库 
require 'common.php';

// 版本限制
if(!(version_compare(PHP_VERSION, '5.5.0', '>') && version_compare(PHP_VERSION, '7.0.0', '<'))){
  die('Requires PHP version between 5.5.x and 5.6.x, Your version: ' . PHP_VERSION );
}
// 自动导入类
spl_autoload_register('ES\CORE\auto_loader');
// 配置文件
$configs = Config::init();
// 是否开启调试
error_reporting($configs->config->debug?E_ALL:0);
// 控制器加载前
$hook = new Hook();
$hook->before_controller();
// 核心路由
$Route = new Route($configs->routes);
// 开始初始化控制器
$Route->resolve();
// 页面执行时间
if($configs->config->timer) echo PHP_EOL.'runtime:'.round((microtime(1)-$_es_timer_begin)*1000,5).'ms';