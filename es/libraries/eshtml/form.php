<?php
namespace es\libraries\eshtml;
/**
* 生成表单
* 支持从ES_model中自动调取_attributes()中的属性及值到表单
* 
* Joe
* -----------------------------------------
* 2015年5月23日09:53:20
* 2015年5月24日12:13:58
* 修复switch松散比较的异常 如0=='任何字符'返回true,改为===的if
* -----------------------------------------
* 2015年8月7日16:18:55
* 为select()增加外置data-options的传入，无传入则调取默认方法。
* -----------------------------------------
* 2015年8月11日16:27:18
* 为input() 放开value属性设置限制
* -----------------------------------------
* 2016年1月21日09:43:04
* 重新布局form标签以配合使用AdminLTE
*/


class Form{
  private $form = '';
  private $model;
  private $instance;
  
/**
* 初始化
* @param ES_model $model
* @param stdClass $instance
* 
* @return
*/  
  public function init(\es\core\Model $model,\stdClass &$instance=null){
    $this->model = $model;
    empty($instance) || $this->instance = &$instance;
    return $this;
  }
  
/**
 * 显示表单
 * @param array $form_attr 表单的其他属性，如action,method等
 * @param array $btn_attr  提交按钮的属性，如[class=>"btn-primary",value=>"提交"],value为按钮上的文字
 * @param bool $print      是否直接打印
 */
  public function display($form_attr=[],$btn_attr=[],$print=TRUE){
    $box_form = '<div class="box box-primary"><form id="ajaxform" role="form" %s>%s</form></div>';
    $form_body = sprintf('<div class="box-body">%s</div>',$this->form);
    $form_footer = '<div class="box-footer"><button id="ajaxformbtn" %s>%s</button></div>';
    
    $base_btn = ['class'=>"btn btn-primary",'value'=>"提交",'type'=>"button"];
    foreach($base_btn as $k=>$v){
      empty($btn_attr[$k]) && $btn_attr[$k] = $v;
    }
    $btn = $btn_attr['value'];unset($btn_attr['value']);
    $form_footer = sprintf($form_footer,$this->_attr($btn_attr),$btn);
    
    strpos($form_body, 'type="file"') && $form_attr['enctype'] = 'multipart/form-data';
    empty($form_attr['method']) && $form_attr['method'] = 'POST';
    if( empty($form_attr['action']) ){
      global $Route;
      $action = '/'.$Route->cmdq['d'].'/'.$Route->cmdq['c'].'/'.$Route->cmdq['m'].'/';
      empty( $Route->cmdq['q'] ) || $action .= $Route->cmdq['q'].'/';
      $form_attr['action'] = $action;
    }
    
    $box_form = sprintf($box_form,$this->_attr($form_attr),$form_body.$form_footer);
    if(!$print ) return $box_form;
    echo $box_form;
  }
  
/**
* 产生一个表单项
* @param string $tag   表单元素
* @param string $name  元素的Name属性与数据库表的字段一致
* @param array  $attr  非Name,Value属性的其他属性,可以含required,help
* 如果设置$attr['help']标明对input填写的规则的一个说明。
*
* @return string;
*/
  private function element($tag,$name,$attr=[]){
    $group = '<div class="form-group %s">%s</div>';
    $input_help = '<p class="help-block">%s</p>';
    $require = in_array('required', $attr)?TRUE:FALSE;
    $segment = '';
    $input_help = empty($attr['help'])?'':sprintf($input_help,$attr['help']);
    
    switch ($tag){
      case 'input':
        $segment = $this->element_input($name,$attr);
      break;case 'file':
        $segment = $this->element_input_file($name, $attr);
      break;case 'textarea':
        $segment = $this->element_textarea($name, $attr);
      break;case 'select':
        $segment = $this->element_select($name, $attr);
      break;
    }
    $group = sprintf($group,$require?' has-success':'',$segment.$input_help);
    $this->form .= $group;
    return $group;
  }
  
  private function element_input($name,$attr){
    $input_with_label = '<label for="%s">%s</label><input %s id="%s" name="%s" placeholder="%s" value="%s">';
    $label = $this->model->_attributes($name);
    $value = empty($this->instance)?'':$this->instance->$name;
    strpos($name,'time')!==FALSE && empty($value) && $value = \es\core\get_format_time();
    empty($attr['class']) && $attr['class'] = 'form-control';
    
    return sprintf($input_with_label,$name,$label,$this->_attr($attr),$name,$name,$label,$value);
  }
  
  private function element_textarea($name,$attr){
    $textarea_with_label = '<label for="%s">%s</label><textarea %s id="%s" name="%s" placeholder="%s">%s</textarea>';
    $label = $this->model->_attributes($name);
    $value = empty($this->instance)?'':$this->instance->$name;
    return sprintf($textarea_with_label,$name,$label,$this->_attr($attr),$name,$name,$label,$value);
  }
  
  private function element_input_file($name,$attr){
    $span_file = '<span class="btn btn-success fileinput-button"><i class="glyphicon glyphicon-plus"></i><span>%s...</span>%s</span>%s';
    $input_file = '<input id="fileupload" type="file" name="files[]" multiple>';
    $_input = '<input id="fileuploadipt" name="%s" class="form-control" style="width:80%%;margin-left:10px;border:none;display:inline-block;">';
    $label = $this->model->_attributes($name);
    return sprintf($span_file,$label,$input_file,sprintf($_input,$name));
  }
  
  private function element_input_checkbox(){
    
    
  }
  private function element_select($name,$attr){
    $method = '__'.$name.'s';
    $select = '';
    if(method_exists($this->model, $method)){
      $options = '';
      $select_with_label = '<label>%s</label><select class="form-control" name="%s" %s>%s</select>';
      $label = $this->model->_attributes($name);
      $value = empty($this->instance)?'':$this->instance->$name;
      foreach($this->model->$method() as $option){
        $_option = '<option value="%s"%s>%s</option>';
        $selected = $option->category_id == $value ?' selected' :'';
        $_option = sprintf($_option,$option->category_id,$selected,$option->category_name);
        $options .= $_option;
      }
      $select = sprintf($select_with_label,$label,$name,$this->_attr($attr),$options);
    }
    return $select;
  }
  
/**
* 标签属性
* @param array $attr 标签忽略name,placeholder,help属性
* @return string
*/
  private function _attr($attr=[]){
    if(empty($attr)) return '';
    is_array($attr) || die('参数必须为数组');
    $str = '';
    foreach($attr as $k=>$v){
      if($k==='format'){
        $k = 'pattern';
        switch($v){
          case 'number':// 数字
            $v = '\d*';
          case 'float':// 小数
            $v = '\d+\.?\d*';
          break;case 'mobile':// 手机号
            $v = '\d{11}';
          break;case 'datetime':// 只是匹配了格式
            $v = '[12]\d{3}-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}';
          break;case 'letter':// 大小写字母
            $v = '[a_zA_Z]*';
          break;
        }
        break;
      }
      in_array( $k,array('name','help','placeholder') ,true) ||
      $str .= is_numeric($k)?" {$v}":" {$k}=\"{$v}\"";
    }
  
    return $str;
  }  
  

//---------------------------------------------------------|
// 元素
//---------------------------------------------------------|
  /**
   * 插入表单中的HTML片段
   */
  public function html_segment($htmlstr=''){
  
  }
  public function input($name,$attr=[]){
    $this->element('input',$name,$attr);
    return $this;
  }
  public function textarea($name,$attr=[]){
    $this->element('textarea',$name,$attr);
    return $this;
  }
  public function select($name,$attr=[]){
    $this->element('select',$name,$attr);
    return $this;
  }
  public function file($name,$attr=[]){
    $this->element('file',$name, $attr);
    return $this;
  }
//---------------------------------------------------------|
}
