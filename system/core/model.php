<?php defined('APPPATH') OR exit('POWERED BY Enozoomstudio');

class ES_model{
  public $db;
  protected $tableName;
  protected $primaryKey;
  
  public function __construct(){
        global $configs;
        empty($configs->database->host) && show_500('数据库未设置');
        $db = $configs->database->driver;
    $this->db = $db::get_instance($configs->database);
  }
/**
 * PHP5内置函数
 * @param unknown $var
 */  
  public function __get($var){
    return $this->$var;
  }    
  
/**
* 表字段及字段说明
* @param string $attr
* 
* 
* 
* @return array/string 
*/
  public function _attributes($attr=''){
/*
    $atts = array('gift_id'=>'ID',
                  'estate_id'=>'楼盘',
                  'gift_category_id'=>'分类',
                  'gift_name'=>'名称',
                  'gift_endtime'=>'截止时间',
                  'gc_id'=>'条件',
                  'gift_total'=>'存货量',
                  'gift_uploadtime'=>'上传时间',
                  'gift_saletime'=>'上架时间',
                  'gift_img'=>'图',
                 );
    return empty($attr)?$atts:(isset($atts[$attr])?$atts[$attr]:$atts);
*/    
    return array();  
  }
/**
* 外键关联
* 
* @return
*/  
  public function _relation(){
/*
    return array(
      'one2one'=>array('gc_id'=>array('table'=>'gift_condition','fkfield'=>'gc_id')),
      'one2more'=>array(),
      'more2more'=>array(),
    );  
*/    
    return array();
  }
  
  
/**
* 获取一组数据
* @param string $where
* @param string $select
* @param bool $returnArray
* @param string $orderby
* @param array $limit 分页 $limit = array($per,$page)
* 
* @return array(obj..)/array(array..)
*/  
  public function _get($where='', $select='*', $orderby='', $limit=array()){
    if(!empty($limit)){
       $per = $limit[0];
       if(count($limit)==1){
        $limit = $per;
       }elseif(count($limit)==2){
         $page = $limit[1];
         $limit = $page.','.$per;
       }
    }
    empty($orderby) && $orderby = "{$this->primaryKey} desc";
    return $this->db->_get($this->tableName,$where,$select,$orderby,$limit);
  }
  
/**
* 方法类似于_get(),参数也形同
* 不同的地方为,_get_with_relation()将相关表数据一并存入返回值中
* 
* 根据条件获取对象或数组
* @param string $where
* @param string $select 字段值
* @param bool $return_array 返回数组还是对象
* @param string $orderby 
* @param array $limit array($per,$page)
* @return Array(Object)
*/
  public function _get_with_relation($where='', $select='*',$orderby='',$limit=array()){
    $rel = array();
    $rals = $this->_relation();
    if(!empty($rals)){
      foreach($rals as $type=>$ary){
        switch($type){
          case 'one2one':
            $key = array_keys($ary);
            $key = $key[0];
            $rel['tableid']=$key;
            $rel['fktable']=$ary[$key]['table'];
            $rel['fkfield']=$ary[$key]['fkfield'];
          break;          
        }        
      }
    }
    if(!empty($rel)){
      return $this->db->_get_with_join($this->tableName,$rel['tableid'],
                                       $rel['fktable'],$rel['fkfield'],
                                       $where,$select,'INNER',$orderby,$limit);
    }                               
    return FALSE;
  }  
  
/**
* 根据ID获取特定一条数据
* @param string $where
* @param string $select
* @param bool $returnArray
* 
* @return obj/array
*/
  public function _get_by_PKID($id='',$select='*'){
    return $this->db->_get_by_PKID($id,$this->primaryKey,$this->tableName,$select);
  }  
/**
* 根据条件获取
* @param string $where
* 
* @return int
*/  
  public function _get_totalnum($where=''){
    return $this->db->_get_totalnum($where,$this->tableName);
  }
/**
* 插入数据
* @param array $array
* 
* @return 
*/  
  public function _insert($array){
    return $this->db->_insert($array,$this->tableName);
  }
/**
* 更新一条数据
* @param undefined $pkid
* @param undefined $array
* 
* @return 
*/  
  public function _update($pkid,$array){
    return $this->db->_update($pkid,$array,$this->primaryKey,$this->tableName);
  }
/**
* 开启事务
* 事务开启后必须commit，否则无法自动提交数据库操作。
*/
  public function trans_start(){
    $this->db->transaction(TRUE);
  }
/**
 * 事务回滚
 */  
  public function rollback(){
      $this->db->rollback();
  }
/**
* 关闭事务并且提交
*/  
  public function trans_end(){
    $this->db->commit();
  }
/**
* 生成一个密码
* @param string $pword
* 
* @return string
*/  
  public function generate_password($pword){
    return md5('hi'.sha1($pword.',').'es');
  }
/**
* 过滤非法字符
* @param array $post
* 
* @return array
*/  
  public function _filter_post($post){
    foreach($post as $k=>$v){
      if(!empty($v) && !is_numeric($v)){
        $post[$k] = $this->db->_escape($v);
      }
    }
    return $post;
  }
/**
 * 将数据array(Obj（id=>,name=>）..)转化成array(id=>name,..)
 * @param array $data 要转化的数据
 * @param string $v 
 * @param string $k 默认为$this->primaryKey
 * @return array
 */  
  public function data2kv($data,$v,$k=''){
    $ary = array();
    empty($k) && $k = $this->primaryKey;
    foreach($data as $d) $ary[$d->$k] = $d->$v;
    return $ary;
  }    
}
?>
