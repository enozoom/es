<?php
namespace es\libraries\Html;

use es\core\Toolkit\ConfigTrait;

use es\core\Toolkit\TimeStatic;
use es\core\Http\HeaderTrait;
class Form{
    use ConfigTrait,HeaderTrait;
    private $form = '';
    private $model;
    private $instance;
    private $cmdq;
  
    /**
    * 初始化
    * @param ES_model $model
    * @param stdClass $instance
    * 
    * @return
    */
    public function init(\es\core\Model\ModelAbstract $model,\stdClass &$instance=null){
        $this->model = $model;
        empty($instance) || $this->instance = &$instance;
        $this->cmdq = $this->getConfigs('cmdq');
        return $this;
    }
      
    /**
     * 显示表单
     * @param array $form_attr 表单的其他属性，如action,method等
     * @param array $btn_attr  提交按钮的属性，如[class=>"btn-primary",value=>"提交"],value为按钮上的文字
     * @param bool $print      是否直接打印
     */
    public function display(Array $form_attr=[],Array $btn_attr=[],$print=TRUE){
        strpos($this->form, 'type="file"') && $form_attr['enctype'] = 'multipart/form-data';
        empty($form_attr['method']) && $form_attr['method'] = 'POST';
        if( empty($form_attr['action']) )
        {
            $action = '/'.$this->cmdq->d.'/'.$this->cmdq->c.'/'.$this->cmdq->m.'/';
            empty($this->cmdq->q) || $action .= $this->cmdq->q.'/';
            $form_attr['action'] = $action;
        }
          
        foreach(['html'=>"提交",'type'=>"button"] as $k=>$v)
        {
            empty($btn_attr[$k]) && $btn_attr[$k] = $v;
        }
        $btn_txt = $btn_attr['html'];unset($btn_attr['html']);
        $btn = sprintf('<button %s>%s</button>',$this->_attr($btn_attr,[]),$btn_txt);
          
        $form = sprintf('<div class="es4-form"><form role="form" %s>%s</form></div>',
                           $this->_attr($form_attr),$this->form.$btn);
        if(!$print ){
            return $form;
        }else{
            $this->httpMime();
            echo $form;
        }
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
    private function element($tag,$name,Array $attr=[]){
        $input_help = '<p class="help">%s</p>';
        $require = in_array('required', $attr)?TRUE:FALSE;
        $segment = '';
        $input_help = empty($attr['help'])?'':sprintf($input_help,$attr['help']);
        
        $method = "element_{$tag}";
        if( method_exists($this, $method) ){
            $segment = $this->{$method}($name,$attr);
        }
        $this->form .= "<div class=\"option\">{$segment}{$input_help}</div>";
        return $this;
    }
  
    private function element_input($name,$attr){
        $input_with_label = '<label for="%s">%s</label><input %s id="%s" name="%s" placeholder="%s" value="%s">';
        $label = $this->model->_attributes($name);
        $value = empty($this->instance)?(isset($attr['value'])?$attr['value']:''):$this->instance->$name;
        if( strpos($name,'time')!==FALSE ){
            empty($value) && $value = 0;
            $value =  TimeStatic::formatTime($value);
        }
        if(isset($attr['value'])) unset($attr['value']);
        empty($attr['type']) && $attr['type'] = 'text';
        return sprintf($input_with_label,$name,$label,$this->_attr($attr),$name,$name,$label,$value);
    }
  
    private function element_textarea($name,$attr){
        $textarea_with_label = '<label for="%s">%s</label><textarea %s id="%s-%d" name="%s" placeholder="%s">%s</textarea>';
        $label = $this->model->_attributes($name);
        $value = empty($this->instance)?'':$this->instance->$name;
        if(isset($attr['value'])){
            empty($value) && empty($this->instance) && $value = $attr['value']; 
            unset($attr['value']);
        }
        
        return sprintf($textarea_with_label,$name,$label,$this->_attr($attr),$name,substr(time(),2),$name,$label,$value);
    }
  
    private function element_file($name,$attr){
        $label = $this->model->_attributes($name);
        $tpl = '<label>%s</label>'.
               '<div class="uploader" data-action="%s"><label><span class="btn btn-green-outline">上传%s</span><input type="file" name="%s" ><input type="hidden" name="%s" value="%s"></label></div>'.
               '<p class="preview"></p>';
        return sprintf($tpl,$label,"/{$this->cmdq->d}/{$this->cmdq->c}/upload/$name",
                            $label,$name,
                             $name.'-tmp',empty($this->instance)?'':$this->instance->$name);
    }
  
    private function element_checkbox($name,$attr)
    {
    
    
    }
  
    private function element_radio($name,$attr){
        $i = rand(1,20);
        $tpl = '<label>%s</label><p><input type="radio" id="%s-%s" name="%s" %s value="1"><label for="%s-%s">是</label><input type="radio" id="%s-%s" name="%s" %s value="0"><label for="%s-%s">否<label></p>';
        $v = empty($this->instance)?0:$this->instance->$name;
        $v ==0 && isset($attr['value']) && $v = $attr['value'];
        return sprintf($tpl,$this->model->_attributes($name),
                        $name,$i,$name,$v?'checked':'',$name,$i,
                        $name,++$i,$name,!$v?'checked':'',$name,$i
                      );
    }
    /**
     * 生成select,将配合JS使用
     * @param string $name
     * @param array $attr  当含有noseljs值时，不自动调用JS
     */
    private function element_select($name,$attr){
        $method = '__'.$name.'s';
        $select = '';
        if(method_exists($this->model, $method)){
          $options = '';
          $select_with_label = '<label>%s</label><div class="select-js"><select data-name="%s" %s>%s</select></div>';
          $label = $this->model->_attributes($name);
          $value = empty($this->instance)?(empty($attr['value'])?'':$attr['value']):$this->instance->$name;
          
          foreach($this->model->$method() as $k=>$v){
            $_option = '<option value="%s"%s>%s</option>';
            $_option = sprintf($_option, $k, $k==$value?' selected':'', $v);
            $options .= $_option;
          }
          
          $selName = isset($attr['data-pids'])?'':'name="'.$name.'" '; //无法配合
          $select = sprintf($select_with_label,$label,$name,$selName.$this->_attr($attr),$options);
        }
        return $select;
    }
  
  
    /**
    * 标签属性
    * @param array $attr 标签忽略name,placeholder,help属性
    * @return string
    */
    private function _attr(Array $attr=[],Array $filter=['name','help','placeholder']){
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
          }
          
          in_array( $k,$filter ,true) || $str .= is_numeric($k)?" {$v}":" {$k}=\"{$v}\"";
        }
        return $str;
    }  
  

/*---------------------------------------------------------
 * 元素
 * -------------------------------------------------------*/
    /**
      * 插入表单中的HTML片段
    */
    public function segment($htmlstr=''){
        $this->form .= sprintf('<div class="option">%s</div>',$htmlstr);
        return $this;
    }
    
    public function segmentLabel($name,$htmlstr=''){
        return $this->segment( sprintf('<label>%s</label><div>%s</div>',$this->model->_attributes($name),$htmlstr) );
    }
    
    public function input($name,$attr=[]){
        return $this->element('input',$name,$attr);
    }
    public function textarea($name,$attr=[]){
        return $this->element('textarea',$name,$attr);
    }
    public function select($name,$attr=[]){
        return $this->element('select',$name,$attr);
    }
    public function file($name,$attr=[]){
        return $this->element('file',$name, $attr);
    }
    public function radio($name,$attr=[])
    {
        return $this->element('radio',$name,$attr);
    }

//---------------------------------------------------------|
}
