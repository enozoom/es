<?php
namespace app\core;
/**
 * HTML页面控制器
 * @author Joe e@enozoom.com
 * 2015年6月25日下午4:06:01
 * -----------------------
 * 2015年9月13日15:36:15
 * 自动载入的css,js,less文件的路径定义在configs/config.eno中。
 *
 */
class Html_controller extends \es\core\Controller{
  public $title;
  public $keywords;
  public $description;
  public $css;
  public $js;
  
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
    $route = $this->_cmdq();
    $theme_path = $this->_configs('theme_path');
    foreach(array('css','js') as $cj){
      $f = $route['c'].'.'.$route['m'].'.'.$cj;
      if($route['d']!=='esadmin')
        file_exists("./theme/{$theme_path}/{$cj}/{$f}") && $this->$cj .= ','.$f;
        isset($data[$cj]) && $this->$cj .= ','.$data[$cj];
        ($i = strpos($this->$cj,','))===0 && $this->$cj = substr($this->$cj,$i+1);
        $this->$cj = str_replace('.'.$cj,'',$this->$cj);
    }
    foreach( get_class_vars(__CLASS__) as $k =>$v ){
      (new \ReflectionClass(__CLASS__))->getProperty($k)->isPublic() && $data[$k] = $this->$k;
    }
  }

  
/**
 * 快捷视图
 * 默认装入<head>标签中的数据
 * 装入以控制器.方法名 命名的js,css文件，在文件存在的情况下
 *
 * @param array $data        需要到view页面的变量
 * @param bool $hf           开启头尾
 * @param string $layout_dir 页面通用头尾文件夹
 * @return void
 */
  protected function view($data=[],$hf=TRUE,$layout_dir='esweb'){
    $theme_path = $this->_configs('theme_path');
    $route = $this->_cmdq();
    $dir = $route['d'].'/'.$route['c'];
    empty($layout_dir) || $dir = $layout_dir;
    $file = "{$dir}/{$route['c']}/{$route['m']}";
    $data['cmdq'] = $route;
    is_object($data) && $data = (array)$data;

    // 载入基本数据
    $this->_data($data);
    
    $hf && $this->_load_header_footer($dir,$data);
    $this->load->view($file,$data);
    $hf && $this->_load_header_footer($dir,$data,'footer');
  }
  
/**
 * 载入头尾页面
 * @param string $dir          头尾页面所在的文件夹
 * @param array $data          需要放置在页面的变量
 * @param string $layout_name  头尾名称
 */
  private function _load_header_footer($dir,$data,$layout_name='header'){
    $route = $this->_cmdq();
    foreach( [$route['c'].'/',''] as $seg ){
      $hf = "{$dir}/{$seg}layout/{$layout_name}";
      if( file_exists(APPPATH.'views/html/'.$hf.'.php') ){
        $this->load->view($hf,$data);
        break;
      }
    }
  }
}