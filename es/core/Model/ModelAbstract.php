<?php
/*
 * 操作数据库
 */
namespace es\core\Model;

use es\core\Toolkit\ConfigTrait;
use es\core\Toolkit\AryTrait;
abstract class ModelAbstract{
  use ConfigTrait,AryTrait;
  
  protected $db;
  protected $tableName;
  protected $primaryKey;
  
  public function __construct()
  {
    $conf = $this->getConfigs('database');
    $db = 'es\\core\\Database\\'.$conf->driver;
    $this->db = $db::get_instance();
    $this->tableName_primaryKey();
  }
  
  /**
   * 自填充数据库表名及主键
   */
  private function tableName_primaryKey(){
      if(empty($this->tableName) || empty($this->primaryKey)){
         $cls = strtolower( substr(($cls = get_class($this)), strrpos($cls, '\\')+1) );
         empty($this->tableName) && $this->tableName = $cls;
         empty($this->primaryKey) && $this->primaryKey = $cls.'_id';
      }
  }
  
  public function __get($var)
  {
    switch($var){
        case 'tableName':
            return $this->db->tablename( $this->$var );
        break;default:
            return $this->$var;
    }
    return null;
  }
  
/**
 * 获取一组数据
 * @param string $where
 * @param string $select
 * @param bool $returnArray
 * @param string $orderby
 * @param array $limit 分页 $limit = [$per,$page]
 *
 * @return [obj,..]/[[],..]
 */
  public function _get($where='', $select='*', $orderby='',Array $limit=[])
  {
    if(!empty($limit)){
      $per = $limit[0];
      if(count($limit)==1){
        $limit = $per;
      }elseif(count($limit)==2){
        $page = $limit[1];
        $limit = $page.','.$per;
      }
    }
    empty($orderby) && $orderby = "{$this->primaryKey} DESC";
    return $this->db->_get($this->tableName,$where,$select,$orderby,$limit);
  }
  
/**
 * 根据ID获取特定一条数据
 * @param string $where
 * @param string $select
 * @param bool $returnArray
 *
 * @return obj/array
 */
  public function _getByPKID($id='',$select='*')
  {
    return $this->db->_get_by_PKID($id,$this->primaryKey,$this->tableName,$select);
  }
  
/**
 * 根据条件获取
 * @param string $where
 *
 * @return int
 */
  public function _getTotalnum($where='')
  {
    return $this->db->_get_totalnum($where,$this->tableName);
  }
  
/**
 * 插入数据
 * @param array $array
 *
 * @return
 */
  public function _insert(Array $array)
  {
    return $this->db->_insert($this->_filterData($array),$this->tableName);
  }
  
/**
 * 更新一条数据
 * @param int $pkid
 * @param array $array
 *
 * @return
 */
  public function _update($pkid,Array $array)
  {
    return $this->db->_update($pkid,$this->_filterData($array),$this->primaryKey,$this->tableName);
  }

/**
 * 删除数据
 * @return
 */
  public function _delete($where)
  {
    return $this->db->_delete($where,$this->tableName);
  }
  
  /**
   * 通过主键进行删除（这里的主键是model类中设置的主键不一定是真实的表主键）
   * @param int $pkid
   */
  public function _deleteByPKID($pkid){
      return $this->_delete( "{$this->primaryKey} = {$pkid}" );
  }
  
/**
 * 开启事务
 */
  public function _transStart()
  {
    $this->db->transaction();
  }
/**
 * 事务回滚
 */
  public function _rollback()
  {
    $this->db->rollback();
  }
/**
 * 关闭事务并且提交
 */
  public function _transEnd()
  {
    $this->db->commit();
  }
  
  
/**
 * 过滤非本表字段的数据
 * @param array $data
 */
  protected function _filterData($data=[])
  {
    foreach($data as $k=>$v){
      if(! key_exists($k, $this->_attributes()) ){
        unset($data[$k]);
      }
    }
    return $data;
  }
  
/**
 * 表字段及对应的字段描述
 * @param string $attr
 * @return empty($attr)?[]:''
 */
  public abstract function _attributes($attr='');
  
  
  
}