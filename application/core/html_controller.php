<?php defined('SYSPATH') OR exit('POWERED BY Enozoomstudio');
/**
 * HTML页面控制器
 * @author Joe e@enozoom.com
 * 2015年6月25日下午4:06:01
 * -----------------------
 * 2015年9月13日15:36:15
 * 自动载入的css,js,less文件的路径定义在configs/config.eno中。
 *
 */
class Html_controller extends ES_controller{
  public $title;
  public $keywords;
  public $description;
  public $css = '';
  public $js = '';
  
  public function __construct(){
    parent::__construct();
    $this->load->helper('html');
  }
  
/**
 * 页面需要的基本数据
 * @param array $data 传往页面的参数
 * ---------------
 * 1.网站基本信息
 */  
  protected function _data(&$data){
 
  }

  
/**
 * 快捷视图
 * 默认装入<head>标签中的数据
 * 装入以控制器.方法名 命名的js,css文件，在文件存在的情况下
 *
 * @param array $data        需要到view页面的变量
 * @param bool $hf          开启头尾
 * @param string $layout_dir    页面通用头尾文件夹
 * @return void
 */
  protected function view($data=array(),$hf=TRUE,$layout_dir='public'){
    global $Route,$configs;
        $theme_path = $configs->config->theme_path;
        
    $route = $Route->cmdq;
    $dir = $route['d'].'/'.$route['c'];
    empty($layout_dir) || $dir = $layout_dir;
    $file = "{$dir}/{$route['c']}/{$route['m']}";
    $data['cmdq'] = $route;
    is_object($data) && $data = (array)$data;
    
    foreach(array('css','js') as $cj){
      $f = $route['c'].'.'.$route['m'].'.'.$cj;
      if($route['d']!=='admin')
            file_exists("./theme/{$theme_path}/{$cj}/{$f}") && $this->$cj .= ','.$f;
      isset($data[$cj]) && $this->$cj .= ','.$data[$cj];
      ($i = strpos($this->$cj,','))===0 && $this->$cj = substr($this->$cj,$i+1);
      $this->$cj = str_replace('.'.$cj,'',$this->$cj);
    }

    // 载入基本数据
    $this->_data($data);
    
    foreach( get_class_vars(__CLASS__) as $k =>$v ){
      $rfc = new ReflectionClass(__CLASS__);// PHP5.4一下不支持虚拟类
      $rfc->getProperty($k)->isPublic() && $data[$k] = $this->$k;
    }
    
    
    if($hf){
      $header = $dir.'/layout/header';
      file_exists(APPPATH.'views/html/'.$header.'.php') && $this->load->view($header,$data);
    }
  
    $this->load->view($file,$data);
  
    if($hf){
      $footer = $dir.'/layout/footer';
      file_exists(APPPATH.'views/html/'.$footer.'.php') && $this->load->view($footer,$data);
    }
  }  
}