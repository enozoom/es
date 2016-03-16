<?php
namespace es\libraries\eshtml;
class Datagrid{
  private $rows;
  private $model;             // 需要的model
  private $output;            // 最终输出
  private $columns = [];      // 列数
  private $dateformat = 'Y-m-d H:i:s';
  private $pagination;
  private $opr = TRUE;        // 是否显示操作按钮(编辑删除)
  
/**
* 初始化数据
* @param array     $rows
* @param ES_model  $model
* 
* @return
*/  
  public function init(&$rows,\es\core\Model $model,$opr=TRUE){
    $this->opr = $opr;
    $this->model = $model;
    $cls = \es\helpers\cls_name($model,1);
    $this->rows =  is_array($rows)?$rows[$cls]:$rows->$cls;
    $this->columns = array();
    if( !empty($this->rows) ){
      foreach($this->rows[0] as $k=>$v){
        $this->columns[$k] = $this->model->_attributes($k);
      }      
    }  
    if(isset($rows['pagination'])||isset($rows->pagination)){
      $this->pagination = is_array($rows)?$rows['pagination']:$rows->pagination;
    }
    return $this;
  }
  
  public function header(){
    $htm = '<thead><tr>';
    foreach($this->columns as $attr=>$val){
//      $th = "<th>{$val}</th>";
      $th = sprintf("<th%s>%s</th>",$attr==$this->model->primaryKey?' class="rowid"':'',$val);
      $htm .= $th;
    }
    $this->opr && $htm.= '<th class="datagrid_opr_btn t_right">操作</th>';
    $htm .= '</tr></thead>';
    $this->output .= $htm;
    return $this;
  }
  public function body(){
    $htm = '<tbody>';
    foreach($this->rows as $row){
      $htm .= '<tr>';
      foreach($this->columns as $attr=>$val){
        $model_method = '__'.$attr.'s';
//        var_dump($model_method);
        if(method_exists($this->model,$model_method) && $row->$attr){
          $_row =  '<span class="'.($attr.'_'.$row->$attr).'">'.$this->model->$model_method($row->$attr).'</span>';
          is_array($_row) && $_row = '无';
        }else{
          $_row = empty($row->$attr)?'':$row->$attr;
          stripos($attr,'time') !== FALSE && $_row = \es\core\get_format_time($_row);
        }
        
        $htm .= "<td>{$_row}</td>";
      }
      
      if($this->opr){
        global $Route;
        extract( $Route->cmdq );
        $pkid_field = $this->model->primaryKey;
        $td_opr= '<td class="datagrid_opr_btn t_right">%s</td>';
        $editor = sprintf('<a href="#" data-href="/%s/%s/index/%d/"><i class="fa fa-edit"></i> 编辑</a>',$d,$c,$row->$pkid_field);
        $delete = sprintf('<a class="dob_btn" href="#" data-href="/%s/%s/del/%d/"><i class="fa fa-times-circle"></i> 删除</a>',$d,$c,$row->$pkid_field);
        $clean =  sprintf('<a class="dob_btn" href="#" data-href="/%s/%s/clean/%d/"><i class="fa fa-refresh"></i> 清缓</a>',$d,$c,$row->$pkid_field);
        $htm .= sprintf($td_opr,$clean.$editor.$delete);
      }
      
      $htm .= '</tr>';
    }
    $htm .= '</tbody>';
    $this->output .= $htm;
    
    return $this;
  }
  public function footer($pagination){
    empty($pagination) || $this->pagination = $pagination;
    $htm = '<tfoot><tr><td colspan="'.(count($this->columns)+($this->opr?1:0)).'">'.$this->pagination.'</td></tr></tfoot>';
    $this->output .= $htm;      
    return $this;
  }
/**
* 分页内容
* @param string $pagination
* 
* @return
*/  
  public function display($pagination=''){
    if(!empty($this->columns)){
      empty($pagination) && $pagination = $this->pagination;
      $this->header()->footer($pagination)->body();
      return '<div class="eno_datagrid_container"><table class="eno_datagrid" data-pkfield="'.$this->model->primaryKey.'" data-tablename="'.$this->model->tableName.'">'.$this->output.'</table></div>';
    }else{
      return '暂无数据';
    }
    
  }
}