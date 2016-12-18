<?php
namespace es\core\Database;
use es\core\Log\LogTrait;
class SQLite extends \SQLite3 implements \es\core\Database\DatabaseInterface
{
    use LogTrait;
    private $dbPath;
    protected static $instance;
    protected $prefix   = '';
    protected $last_query = '';
    public function __construct()
    {
        $this->prefix = $this->getConfig('prefix','database');
        $this->dbPath = APPPATH.'data/sqlite3.db';
        $this->open($this->dbPath);
    }
    
    public static function get_instance()
    {
         self::$instance instanceof self || self::$instance = new self();
         return self::$instance;
    }
    
    public function query($sql)
    {
        $this->last_query = $sql;
        switch( strtoupper(substr($sql,0,strpos($sql, ' '))) ){
            case 'SELECT':
                $SQLite3Result = parent::query($sql);
                $rs = [];
                while ($row = $SQLite3Result->fetchArray(SQLITE3_ASSOC)) {
                    $rs[] = (object)$row;
                }
                return $rs;
            break;case 'INSERT':
                parent::query($sql);
                return $this->lastInsertRowID();
            break;case 'DELETE':case 'UPDATE':
                parent::exec($sql);
                return parent::changes();
            break;
        }

        return FALSE;
    }

    public function _get_by_PKID($id, $pkfield, $tablename, $select = '*')
    {
        $sql = sprintf('SELECT %s FROM %s WHERE `%s` = %s LIMIT 1',
                       $select,
                       $this->tablename($tablename),
                       $pkfield,
                       $this->__ns($id));
        $r = $this->querySingle($sql,TRUE);
        return empty($r)?NULL:(object)$r;
    }

    public function last_query()
    {
        return $this->last_query;
    }


    public function _insert(array $data, $tablename)
    {
        if(empty($data)) return 0;
        $values = '';
        foreach($data as $v) $values[] = $this->__ns($v);
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)",
                       $this->tablename($tablename),
                       '`'.implode('`,`',array_keys($data)).'`',
                       implode(',',$values));
        return $this->query($sql);
    }

    public function _update($pkid, array $data, $pkfield, $tablename)
    {
        if(empty($data)) return 0;
        $values = '';
        foreach($data as $k=>$v) $values .= sprintf(',`%s` = %s',$k,$this->__ns($v));
        
        $sql = sprintf("UPDATE %s SET %s WHERE `%s` = %s",
            $this->tablename($tablename),
            substr($values,1),
            $pkfield,
            $this->__ns($pkid));
        return $this->query($sql);
    }



    public function _get($tablename, $where = '', $select = '*', $orderby = FALSE, $limit = '')
    {
        $tablename = $this->tablename($tablename);
        empty($select) && $select = '*';
        if( $select != '*' && strpos($select, 'AS') === FALSE ){
            $select = '`'.str_replace(',','`,`', preg_replace('/\s*/','',$select)).'`';
        }
        
        $sql = "SELECT {$select} FROM `{$tablename}`";
        empty($where)   ||  $sql .= " WHERE ".$where;
        empty($orderby) ||  $sql .= " ORDER BY $orderby";
        empty($limit)   ||  $sql .= " LIMIT $limit";
        
        $SQLite3Result = parent::query($sql);
        if(!$SQLite3Result->numColumns() ){
            return;
        }
        while ($row = $SQLite3Result->fetchArray(SQLITE3_ASSOC)) {
            yield (object)$row;
        }
    }

    public function _delete($where, $tablename)
    {
        $sql = sprintf('DELETE FROM %s%s',$this->tablename($tablename),empty($where)?'':" WHERE {$where}");
        return $this->query($sql);
    }

    public function close()
    {
        parent::close();
    }

    public function _get_totalnum($where, $tableName, $distinct = '')
    {
        $sql = sprintf('SELECT COUNT(%s) FROM %s%s',
                        empty($distinct)?'*':$distinct,
                        $this->tablename($tableName),
                        empty($where)?'':" WHERE {$where}");
        return $this->querySingle($sql);
    }

    public function tablename($tablename,$prefix=''){
        empty($prefix) && $prefix = $this->prefix;
        if(strpos($tablename,$prefix) === FALSE || strpos($tablename,$prefix) > 0){
            $tablename = $prefix.$tablename;
        }
        return $tablename;
    }
    
    /**
     * 返回一个字符串如果$v是数字在直接返回，如果是字符则加上单引号返回。
     * @param string|int $v
     * @return string|int
     */
    private function __ns($v){
        return is_numeric($v)?$v:sprintf("'%s'",parent::escapeString($v));
    }
    
    public function select_db($dbname, $prefix = '')
    {}
    public function dbname()
    {}
    public function transaction()
    {}
    public function commit()
    {}
    public function rollback()
    {}
}