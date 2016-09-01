<?php
namespace es\core\Database;

interface DatabaseInterface{

/**
 * 获取一个单例
 * @return DatabaseInterface 实现类
 */
  public static function get_instance();
/**
 * 重新选择当前数据库
 * @param string $dbname 数据库名
 * @param string $prefix 数据库表前缀
 * @return void
 */
  public function select_db($dbname,$prefix='');
/**
 * 关闭数据库连接
 * @return void
 */
  public function close();
/**
 * 最后一条SQL执行语句，无论是否执行成功
 * @return string
 */
  public function last_query();
/**
 * 当前数据库名称
 * @return string
 */
  public function dbname();
  
/*====================================|
|
|  DML
|
=====================================*/
  
/**
 * 插入一条数
 * @param array  $data      插入的内容
 * @param string $tablename
 * @return int 成功返回新插入的数据的主键ID，失败返回0
 */
  public function _insert(Array $data,$tablename);
/**
 * 更新一条数
 * @param int $pkid         被更新数据的主键ID
 * @param array $data       更新的内容
 * @param string $pkfield   被更新表的主键字段名
 * @param string $tablename 被更新表名
 * @return int 成功返回被修改的数据行主键ID，失败返回0
 */
  public function _update($pkid,Array $data,$pkfield,$tablename);
/**
 * 删除数据
 * @param string $where
 * @param string $tablename
 * @return int 受影响的行数
 */
  public function _delete($where,$tablename);
/**
 * 获取一组数据
 * @param sting $tablename 要查询的表
 * @param string $where    查询条件
 * @param string $select   查询字段
 * @param string $orderby  排序规则
 * @param string $limit    行数限制
 * @return array [obj,..]
 */
  public function _get($tablename,$where='',$select='*',$orderby=FALSE,$limit='');
/**
 * 根据主键获取一条数据
 * @param int $id           主键ID
 * @param string $pkfield   主键字段
 * @param string $tablename 表名
 * @param string $select    查询字段
 * @reutrn obj
 */
  public function _get_by_PKID($id,$pkfield,$tablename,$select='*');
/**
 * 获取满足条件的总行数
 * @param string $where     查询条件
 * @param string $tableName 表名
 * @param string $distinct  根据某字段去重复
 * @return int
 */
  public function _get_totalnum($where,$tableName,$distinct='');

/**
 * 执行一条sql语句
 * @param string $sql
 * @return mixed
 */
  public function query($sql);
  
/*====================================|
|
|  DCL
|
=====================================*/
  
/**
 * 事务的开启
 * @param string $io
 * @return void
 */
  public function transaction();
/**
 * 操作提交,事务关闭
 * @return void
 */
  public function commit();
/**
 * 操作回滚
 */
  public function rollback();
}