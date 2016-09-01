<?php
namespace es\core\Exception;
use es\core\Toolkit\Config;
class Exception extends \Exception{
    use Config;
    public function trace(){
        $message = sprintf('文件:%s；行号:%s；错误信息:%s',$this->file,$this->line,$this->message);
        if( $this->getConfig('debug') ){
            die( $message );
        }else{
            $this->logger($message);
            exit();
        }
        
    }
    public function logger($debug=''){
        $this->getConfigs('logger')->debug($debug);
    }
}