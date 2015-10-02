<?php //use MtHaml\Autoloader;
defined('APPPATH') OR exit('POWERED BY Enozoomstudio');
require SYSPATH.'core/model.php';
/**
 * 载入model,library,helper,view
 * @author Joe
 */
class ES_load{	
	private $dir_model	 = 'models/';
	private $dir_view 	 = 'views/';
	private $dir_helper  = 'helpers/';
	private $dir_library = 'libraries/';

/**
 * 获取配置参数
 * @param string $file 配置文件的文件名
 * @param string $k 需要获取$k的值
 */	
	public function config($file='config',$k=''){
		global $configs;
		if(empty($k)) return $configs->$file;
		return isset($configs->$file->$k)? $configs->$file->$k: false;
	}
	
/**
* 载入类库
* @param string $cls   库类
* @param string $alias 别名
* @return ES_load
*/	
	public function library($cls,$alias=FALSE){
		$controller = ES_controller::get_instance();
		
		$file = APPPATH.$this->dir_library.$cls.'.php';
		if(!is_file($file)){ // 如果APPPATH中没有，则去SYSPATH中查找
			$file = SYSPATH.$this->dir_library.$cls.'.php';
		}
		
		is_file($file) || show_500('library不存在，'.$file);
		
		require($file);
		
		if( ($idx = strpos($cls,'/')) !== FALSE  ){
			$cls = substr($cls,$idx+1);
		}
		
		empty($alias) && $alias = $cls;
		$cls = ucfirst($cls);
		$_cls = new $cls();
		$controller->$alias = &$_cls;
		return $this;
	}

/**
* 载入model
* @param string $cls   ES_model子类名
* @param string $alias 别称
* 
* @return ES_load
*/	
	public function model($cls,$alias=FALSE){
		$controller = ES_controller::get_instance();
		$file = APPPATH.$this->dir_model.$cls.'.php';
		is_file($file) || show_500('model不存在，'.$file);
		require($file);
		empty($alias) && $alias = $cls;
		$cls = ucfirst($cls);
		$_model = new $cls();
		$_model instanceof ES_model || show_500($cls.'非ES_model子类');
		$controller->$alias = &$_model;
		return $this;
	}
/**
* 载入helper函数包
* @param string $helper 如果导入多个helper，用 “,” 分割
* 
* @return ES_load
*/	
	public function helper($helper){
		$helpers = array();
		if( is_array($helper) ){
			$helpers = $helper;
		}else{
			if( is_string($helper) && strpos($helper,',') ){
				 $helpers = explode(',',$helper);
			}else{
				$helpers[] = $helper;
			}
		}
		// Fixed 2015-04-21 22:14:24  修正不能同时载入SYSPATH,APPPATH下的同名文件
		foreach($helpers as $helper){
			$file = APPPATH.$this->dir_helper.$helper.'_helper.php';
			is_file($file) && require($file);
			$file = SYSPATH.$this->dir_helper.$helper.'_helper.php';
			is_file($file) && require($file);
		}
		return $this;
	}
	
/**
* 载入视图
* @param string $dir html/php视图的文件路径，application/views下的什么位置
* @param array $data
* 
* @return void
*/	
	public function view($dir,$data=array()){
		!empty($data) && is_array($data) && extract($data);
		$file = APPPATH.$this->dir_view.'html/'.$dir.'.php';
		is_file($file) || show_500('view文件不存在,'.$file);
		ob_start();
		include($file);
		$output = ob_get_contents();
		@ob_end_clean();
		$controller = ES_controller::get_instance();
		$controller->output->append_output($output);
	}
/**
 * 解析haml
 * @param string $dir haml视图的文件路径，application/views下的什么位置
 * @param array $data
 */
	public function haml($dir,$data=array()){
/*
	    $dir_cache = APPPATH.'cache';
		$file = APPPATH.$this->dir_view.'haml/'.$dir.'.haml';
		
		require SYSPATH.'libraries/MtHaml/Autoloader.php';
		MtHaml\Autoloader::register();
		
		$haml = new MtHaml\Environment('php');
		$executor = new MtHaml\Support\Php\Executor($haml, array('cache' => $dir_cache.'/haml'));
		$output = $executor->render($file,$data);
		$controller = ES_controller::get_instance();
		$controller->output->append_output($output);
*/
	}
	

}
?>