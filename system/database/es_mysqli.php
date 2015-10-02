<?php 
defined('SYSPATH') OR exit('POWERED BY Enozoomstudio');
/**
* --------------------------------------------------------------------------
* | 直接作用于数据库
* | 用来在单站多库中调取相应数据
* | 仅仅进行查询操作！
* --------------------------------------------------------------------------
* 
* JOE
* 2014年6月24日14:19:45
* 
* --------------------------------------------------------------------------
* | BUGFIX                                                                  |
* --------------------------------------------------------------------------
* 2014-06-25 
* 修正 query($sql)中如果sql异常 MySQLi_Result无法生成的问题
* 增加 _get_totalnum()
* --------------------------
* 2014-08-08 13:06:56
* 优化 get_totalnum() 不再通过_get()的对象数量返回值
* --------------------------
* 2015年5月24日23:30:56
* 增加delete()
* --------------------------
* 2015年6月8日15:03:06
* 修复close()方法的判断条件错误
* --------------------------
* 2015年7月11日10:40:34
* get_totalnum($where,$tablename,$distinct)
* 增加第三个参数$distinct对某字段过滤重复
* --------------------------
* 2015年7月17日09:16:45
* 更改tablename() 由protected到public
* --------------------------
* 2015年8月4日10:55:01
* 增加dbname() 获取当前数据库名
* --------------------------
* 2015年8月8日10:42:35
* select_db($dbname,$prefix)增加第二个参数表名前缀
* tablename($tablename,$prefix='e_') 增加第二个参数表名前缀
* --------------------------
* 2015年9月14日16:01:21
* 对事务进行修正，其正确使用方式为：
* $this->transaction();
*   $id   = $this->_insert();
*   $id2  = $this->_update();
*   if(empty($id)||empty($id2)){
*     $this->rollback();
*   }
* $this->commit();
* 
*/
class ES_Mysqli{
  protected $host     = '';
  protected $user     = '';
  protected $password = '';
  protected $dbname   = '';
  protected $prefix   = '';
  protected $mysqli;
  protected static $instance;
  protected $last_query = '';
  protected $port = 3306;
 
  public function __construct($configs){
    foreach( get_class_vars(__CLASS__) as $var=>$val){
    	empty($configs->$var) || $this->$var = $configs->$var;
    }
    empty($configs->host) || $this->conn();
  }
  
/**
* 获取一个单例
* 
* @return ES_Mysqli
*/  
  public static function get_instance($configs){
  	self::$instance instanceof self || self::$instance = new self($configs);
    return self::$instance;    
	}
  
/**
* 连接数据库，返回一个mysqli对象
* @return Mysqli
*/  
  protected function conn(){
  	if(!$this->mysqli instanceof Mysqli){
	    $mysqli = new Mysqli($this->host, $this->user, $this->password, $this->dbname,$this->port);
	    if(mysqli_connect_error()){
	      printf("E_Database connect failed: %s\n", mysqli_connect_error());
	    }else{
	      $this->mysqli = &$mysqli;
	      $this->mysqli->set_charset("utf8");
	    }			
	}
  	
  }
/**
* 开启或者关闭事务
* @param bool $io 默认关闭
* @return void
*/  
 	public function transaction($io = FALSE){
 	  $this->transaction_helper();
	}
  
/**
 * 获取当前autocommit的值
 */	
    protected function autocommit_status(){
      $result = $this->mysqli->query("SELECT @@autocommit");
      return $result->fetch_row()[0];
    }
    
/**
* 提交事务，技术事务
* @return void
*/
	public function commit(){
		$this->mysqli->commit();
	}
	
/**
 * 回滚
 */
	public function rollback(){
	  $this->mysqli->rollback();
	}
	
/**
 * 事务助手
 * PHP5.5以上有新事务功能
 */
	protected function transaction_helper(){
	  if(PHP_VERSION >= 5.5){
	    $this->mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
	  }else{
	    $this->mysqli->query('START TRANSACTION');
	  }
	}
/**
* 选择（更换）数据库
* @param $dbname 
* @return void
*/
	public function select_db($dbname,$prefix=''){
		$this->mysqli->select_db($dbname);
		$this->dbname = $dbname;
		empty($prefix) || $this->prefix = $prefix;
	}
  
/**
* 关闭连接
* @return void
*/  
  public function close(){
    if(!empty($this->mysqli)){
    	$this->mysqli->close();
    	$this->mysqli = NULL;
    }
  }
  
/**
* 插入一条数据
* @param array $data array(field=>val..)
* @param string $tablename 
* 
* @return int 新插入的PKID,如果插入失败返回0，无自增长主键的始终返回1
*/  
	public function _insert($data,$tablename){
		$sql = "INSERT INTO `{$this->prefix}{$tablename}`";
		$fields = '';
		$vals = '';
		foreach($data as $field=>$val){
			$fields .= ",`{$field}`";
			$vals .= ",'". $this->_escape($val) ."'";
		}
		
		$sql .= '('. substr($fields,1) .') VALUES ';
		$sql .= '('. substr($vals,1) .')';
		
		$result = $this->query($sql);
		
		// 以解决无自增长主键的表返回始终1
		$i = $this->mysqli->insert_id;
		$result && $i == 0 && $i = 1;
		
		return $result ? $i : 0;
	}
	
/**
* 更新一条数据
* @param int 	$pkid
* @param array 	$data
* @param string $pkfield
* @param string $tablename
* 
* @return int 被更新的主键
*/  
  public function _update($pkid,$data,$pkfield,$tablename){
		$set = '';
		foreach($data as $field=>$val){
			$set .= ", `{$field}` = '{$val}'";
		}
		$set = substr($set,1);
		$where = "`{$pkfield}` = '".$this->_escape($pkid)."'";
		
		// 判断有满足更新条件的数
		$total = $this->_get_totalnum($where, $tablename);
		if($total == 0)return 1;// 无满足条件数据则直接返回更新成功
		
		$tablename = $this->tablename($tablename);
		$sql = "UPDATE `{$tablename}` SET {$set} WHERE {$where}";
		$result = $this->query($sql);
		
		return $result?$pkid:0;
	}
	
/**
 * 删除数据
 * @param string $where
 * @param string $tablename
 */	
	public function delete($where,$tablename){
		$sql = "DELETE FROM `{$this->prefix}{$tablename}` WHERE {$where}";
		return $this->query($sql);
	}
	
/**
* 获取一组对象
* @param string $tablename 数据表名
* @param string $where     条件
* @param string $select    字段
* @param string $orderby   排序
* @param string $limit     数量
* 
* @return array(object..)
*/  
  public function _get($tablename,$where='',$select='*',$orderby=FALSE,$limit=FALSE){
    $tablename = $this->tablename($tablename);
    
    empty($select) && $select = '*';
    $select == '*' || $select = '`'.str_replace(',','`,`', clean_wordblank($select)).'`';

    $sql = "SELECT {$select} FROM `{$tablename}`";
    
    empty($where)   ||  $sql .= " WHERE ".$this->_where($where);
    
    empty($orderby) ||  $sql .= " ORDER BY $orderby";
    empty($limit)   ||  $sql .= " LIMIT $limit";
    return $this->query($sql);
  }
  
/**
* 获取一个对象
* @param int    $id        ID
* @param string $pkfield   主键字段
* @param string $tablename 数据表名
* @param string $select    字段
* 
* @return object
*/
  public function _get_by_PKID($id,$pkfield,$tablename,$select='*'){
    $result = $this->_get($tablename,
    					"`{$pkfield}` = '".$this->_escape($id)."'",
    					$select);
    return empty($result)?FALSE:$result[0];
  }
  
/**
* 按条件获取行数
* @param string $where     条件
* @param string $tableName 数据表名
* @param string $distinct  含过滤字段的唯一字段
* @return int
*/ 
  public function _get_totalnum($where,$tableName,$distinct=''){
	$tableName = $this->tablename($tableName);
    $sql = 'SELECT COUNT(%s) AS count FROM %s';
    $sql = sprintf($sql,empty($distinct)?'*':"DISTINCT $distinct",$tableName);
    empty($where) ||  $sql .= " WHERE {$where}";
    $count = $this->query($sql);
    return (int)$count[0]->count;
  }

/**
* 获取表关联数据
* 
* @param string $tablename 当前表
* @param string $relid		关联表外键
* @param string $fktable	关联表
* @param string $tableid	表中有外键关联的字段
* @param string $where		条件
* @param string $select     字段
* @param string $rel        关联操作符 LEFT,RIGHT,INNER
* @param string $orderby	排序
* @param string $limit		数量，起点数
* 
* @return array(obj..)
*/
	public function _get_with_join($tablename,
                                   $tableid,
                                   $fktable,
                                   $relid,																 
                                   $where='',
                                   $select='*',
                                   $rel='INNER',																 
                                   $orderby=FALSE,
                                   $limit=FALSE){
																 	
		empty($select) && $select = '*';
		$select == '*' || 
		$select = '`'.str_replace(array(',','.'),array('`,`','`.`'),clean_wordblank($select)).'`';
		
		$tablename = $this->tablename($tablename);
		$fktable = $this->tablename($fktable);
		
		$sql = "SELECT {$select} FROM `{$tablename}`";
		$sql .= " {$rel} JOIN `{$fktable}` ON `{$tablename}`.`{$tableid}` = `{$fktable}`.`{$relid}` ";
		
    empty($where)   ||  $sql .= " WHERE ".$this->_where($where);;
    empty($orderby) ||  $sql .= " ORDER BY $orderby";
    empty($limit)   ||  $sql .= " LIMIT $limit";
    
    return $this->query($sql);
	}

/**
* 对where进行过滤
* @param string $where
* 
* @return string
*/
	public function _where($where){
		if(empty($where)) return '';
		// 基本过滤,条件句中不能含有DML,DDL语句
		foreach(array('select','delete','update','drop','create','alter') as $dl){
			if(!stripos($where,$dl)===FALSE){
				die('SQL含非法字符');
			}
		}
		return $where;
	}
	
	public function _escape($str=''){
		return empty($str)?'':$this->mysqli->real_escape_string($str);
	}

/**
* 执行SQL
* @param string $sql
* 
* @return array(obj..)
*/  
  public function query($sql){
  	$this->last_query = $sql;  	
    if(!$_result = $this->mysqli->query($sql)){
      log_msg($this->mysqli->error.'<br>'.$sql,'MYSQL数据异常');
      return FALSE;
	}

    if(!$_result instanceof MySQLi_Result ){// INSERT,UPDATE返回值不是Result
      if(!$_result){
        log_msg($this->mysqli->error.'<br>'.$sql,'MYSQL数据异常');
      }
      return $_result;
    }
    
    $result = array();
    if($_result->num_rows>0){
      while($obj = $_result->fetch_object()){
        $result[] = $obj;
      }
    }
    $_result->close();
//    $this->close();
    return $result;
  }
  
/**
* 上次执行成功的SQL语句
* 
* @return string
*/  
  public function last_query(){
		return $this->last_query;
	}
/**
* 实际表名
* @param string $tablename
* @param string $prefix 默认前缀
* @return string
*/	
	protected function tablename($tablename,$prefix='e_'){
		if(strpos($tablename,$prefix) === FALSE || strpos($tablename,$prefix) > 0){
			$tablename = $this->prefix.$tablename;         
    	}
    	return $tablename;
	}
/**
 * 当前数据库名
 */	
	public function dbname(){
		return $this->dbname;
	}
}