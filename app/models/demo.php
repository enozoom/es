<?php namespace app\models;
/**
 * 数据模型
 * @author ES3.016022 
 * 2016-02-19 11:53:14
 */
class Demo extends \es\core\Model{
  protected $tableName = 'demo';
  protected $primaryKey = 'demo_id';  
  public function _attributes($attr=''){
    $atts = array(
                  demo_id => 'demo_id',
                  demo_name => 'demo_name',
    );
    return empty($attr)?$atts:(isset($atts[$attr])?$atts[$attr]:FALSE);
  }
}