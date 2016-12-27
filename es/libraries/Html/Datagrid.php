<?php
namespace es\libraries\Html;
use es\core\Model\ModelAbstract;
use es\core\Toolkit\ConfigTrait;
class Datagrid{
  use ConfigTrait;
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
* @param Model  $model
* 
* @return
*/  
  public function init(&$rows,ModelAbstract $model,$opr=TRUE){
    $this->opr = $opr;
    $this->model = $model;
    $cls = $this->clsName($model,1);
    $this->rows =  is_array($rows)?$rows[$cls]:$rows->$cls;
    $this->columns = [];
    
    $row = null;
    $flag = empty($this->rows);
    if( $this->rows instanceof \Generator ){
        $row = $this->rows->current();
    }else{
        $flag || $row = $this->rows[0];
    }
    
    if( !empty($row) ){
      foreach($row as $k=>$v){
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
    $this->opr && $htm.= '<th class="datagrid-opr-btn t-right">操作</th>';
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
        if(method_exists($this->model,$model_method) && $row->$attr){
          $mm = $this->model->$model_method($row->$attr);
          is_array($mm) && $mm = '异常';
          $_row =  '<span class="'.($attr.'_'.$row->$attr).'">'.$mm.'</span>';
          is_array($_row) && $_row = '无';
        }else{
          $_row = empty($row->$attr)?'':$row->$attr;
          stripos($attr,'time') !== FALSE && !empty($_row) && $_row = date($this->dateformat,$_row);
        }
        
        $htm .= "<td>{$_row}</td>";
      }
      
      if($this->opr){
          $cmdq = $this->getConfigs('cmdq');
          $pkid_field = $this->model->primaryKey;
          $td_opr= '<td class="datagrid-opr-btn t-right">%s</td>';
          $tpl = '<a title="修改" data-href="/%s/%s/id/%d/"><i class="ion-compose"></i></a>';
          $editor = sprintf($tpl,$cmdq->d,$cmdq->c,$row->$pkid_field);
          $tpl = '<a title="删除" class="no-bind-a" data-href="/%s/%s/del/%d/"><i class="ion-trash-a"></i></a>';
          $delete = sprintf($tpl,$cmdq->d,$cmdq->c,$row->$pkid_field);
          $htm .= sprintf($td_opr,$editor.$delete);
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
      return '<div class="eno-datagrid-container"><table class="eno-datagrid" data-pkfield="'.$this->model->primaryKey.'" data-tablename="'.$this->model->tableName.'">'.$this->output.'</table></div>';
    }else{
      return '<p class="nodata"><i class="ion-alert-circled">&nbsp;</i>暂无数据..</p>';
    }
    
  }
  
  /**
   * 一个基于MODEL类名的复数变量名，直接在model类名的末尾加s
   * @param \es\core\Model\Model $m
   * @return string
   */
  public function clsName(ModelAbstract $m)
  {
      $cls = get_class($m);
      $cls = substr($cls, strrpos($cls, '\\')+1 );
      return strtolower($cls.'s');
  }
}