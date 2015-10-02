<?php
defined('SYSPATH') OR exit('POWERED BY Enozoomstudio');
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
 */

// 计时器
$_es_timer_begin = microtime(1);

// 版本
define('ES_VERSION','2.1151002');
// 通用函数库 
require 'common.php';

// 自动导入类
spl_autoload_register('auto_loader');
// 配置文件
$configs = Config::init();
//是否开启调试
error_reporting($configs->config->debug?E_ALL:0);
// 核心路由
$Route = new Route($configs->routes);

$Route->resolve();
// 页面执行时间
if($configs->config->timer) echo PHP_EOL.'runtime:'.(microtime(1)-$_es_timer_begin).'s';