<?php defined('APPPATH') OR exit('POWERED BY Enozoomstudio');
/**
 * 文章
 * @author Joe
 * 2015年5月22日21:10:22
 */
class Demo_model extends ES_model{
  protected $tableName = 'demo';
  protected $primaryKey = 'demo_id';  
  public function _attributes($attr=''){
    $atts = array(
      'demo_id'=>'ID',
      'demo_name'=>'名字',
    );
    return empty($attr)?$atts:(isset($atts[$attr])?$atts[$attr]:FALSE);
  }
}