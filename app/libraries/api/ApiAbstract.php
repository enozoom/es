<?php
namespace app\libraries\api;
use es\core\Controller\AbstractController;
use es\core\Toolkit\Ary;
use es\core\Http\Request;
abstract class ApiAbstract{
    use ary,Request;
    public function __construct(){
        $this->load = $this->ctrl('load');
    }
    
    public function __get($attr){
        if( property_exists($this, $attr) ){
            return $this->$attr;
        }
        if( property_exists($this->ctrl(), $attr) ) {
            return $this->ctrl($attr);
        }
        die( __FILE__.'属性'.$attr.'不存在' );
    }
    
    protected function ctrl($attr=''){
        $ES = AbstractController::getInstance();
        if(!empty($attr) && property_exists($ES, $attr) ){
            return $ES->$attr;
        }else{
            return $ES;
        }
    }
    /**
     * 获取POST提交数据且必须包含$requires中存在的数据
     * @param array $requires
     * @return bool
     */
    protected function __postRequires($requires=[]){
        return $this->reqestMethod('post') && $this->isRequired($requires,$_POST);
    }
    
    /**
     * logger->debug的别名
     * @param string $msg
     */
    protected function __log($msg){
        global $CONFIGS;
        $CONFIGS->logger->debug($msg);
    }
}