<?php
namespace es\core\Controller;

use es\core\Http\Response;
use es\core\Http\Request;

class HtmlController extends AbstractController
{
    use Response,Request;
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
   * 面包屑路径
   */
  protected function crumbs($category_id=0){
  /**
   * $crumbs = [
   *             ['标题1','路径2'],
   *             ['标题2','路径2'],
   *             '标题3只有标题',
   *           ];
   */
    return [];
  }
  
  /**
   * 输出到页面的变量们
   * @param array $data
   */
  protected function __data__(&$data)
  {
    $cmdq = $data['cmdq'];
    $theme_path = $this->getConfig('theme_path');
    foreach(array('css','js') as $cj){
      $f = $cmdq['d'].'.'.$cmdq['c'].'.'.$cmdq['m'].'.'.$cj;
      if($cmdq['d']!=='esadmin')
        isset($data[$cj]) && $this->$cj .= ','.$data[$cj];
        file_exists("./theme/{$theme_path}/{$cj}/{$f}") && $this->$cj .= ','.$f;
        ($i = strpos($this->$cj,','))===0 && $this->$cj = substr($this->$cj,$i+1);
        $this->$cj = str_replace('.'.$cj,'',$this->$cj);
    }
    foreach( get_class_vars(__CLASS__) as $k =>$v ){
      (new \ReflectionClass(__CLASS__))->getProperty($k)->isPublic() && $data[$k] = $this->$k;
    }
    $data['crumbs'] = $this->crumbs();
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
  protected function view($data=[],$hf=TRUE,$layout_dir='')
  {
    $cmdq = $this->getConfigs('cmdq');
    
    empty($layout_dir) && $layout_dir = $cmdq->d;
    is_array($data) || $data = json_decode( json_encode($data) ,TRUE);
    $theme_path = $this->getConfig('theme_path');
    $dir = $cmdq->d.'/'.$cmdq->c;
    empty($layout_dir) || $dir = $layout_dir;
    $file = "html/{$dir}/{$cmdq->c}/{$cmdq->m}";
    $data['cmdq'] = (array)$cmdq;
  
    // 载入基本数据
    $this->__data__($data);
  
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
    foreach( [$data['cmdq']['c'].'/',''] as $seg ){
      $hf = "{$dir}/{$seg}layout/{$layout_name}";
      if( file_exists(APPPATH.'views/html/'.$hf.'.php') ){
        $this->load->view('html/'.$hf,$data);
        break;
      }
    }
  }
}