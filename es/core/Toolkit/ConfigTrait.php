<?php
namespace es\core\Toolkit;

/**
 * 获取全局变量$CONFIGS的相关值
 * 已知$CONFIGS包含有
 * [
 *   // 基本配置文件
 *   config=>[],
 *   // 当前的控制器，方法，控制器文件夹，方法参数
 *   cmdq=>[],
 *   // 数据库配置文件
 *   database=>[],
 *   // 钩子类
 *   hook=>,
 *   // 日志类
 *   logger=>,
 *   // 路由配置文件
 *   routes=>[],
 * ]
 * 
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年7月9日下午1:40:53
 */
trait ConfigTrait{
/**
 * 通过键获取对应的键的值
 * @param string $key
 * @param string $configName
 */
  protected function getConfig($key,$configName='config')
  {
    global $CONFIGS;
    if(!empty( $CONFIGS->$configName ) && 
       !empty( $CONFIGS->$configName->$key )){
      return $CONFIGS->$configName->$key;
    }
    return null;
  }
  
/**
 * 通过文件名获取文件所有的配置信息
 * @param string $configName
 */
  protected function getConfigs($configName='config')
  {
    global $CONFIGS;
    return empty($CONFIGS->$configName)?null:$CONFIGS->$configName;
  }
/**
 * 设置当前的配置信息
 * @param string $val        新值
 * @param string $key        键
 * @param string $configName 配置文件
 * @return 当前对象  $this->setConfig(1,'cache')->setConfig('es_','prefix','database');
 */
  protected function setConfig($val,$key,$configName='config')
  {
    global $CONFIGS;
    $CONFIGS->{$configName}->{$key} = $val;
    return $this;
  }
}