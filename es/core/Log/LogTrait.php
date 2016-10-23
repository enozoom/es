<?php

namespace es\core\Log;
/**
 * 输出日报功能
 * 这个trait引用了ConfigTrait,与控制器(控制器基类已引用ConfigTrait)有重名方法。
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年10月23日下午1:36:53
 */
use es\core\Toolkit\ConfigTrait;
trait LogTrait {
    use ConfigTrait{
        setConfig as private _setConfig;
        getConfig as private _getConfig; 
        getConfigs as private _getConfigs;
    }
    
    /**
     * 调试打印
     * @param string $message  如果$message为对象则必须使用debug
     * @param string $logLevel debug(能打印对象)|error(会打印错误堆栈)
     */
    protected function log($message,$logLevel='debug'){
        (is_array($message) || is_object($message) ) && $logLevel == 'error' && $logLevel = 'debug';
        $logger = $this->_getConfigs('logger');
        $logger->$logLevel($message);
    }
}