<?php defined('APPPATH') OR exit('POWERED BY Enozoomstudio');
/**
* 生成ES_model的实现类
* @author Joe e@enozoom.com
* 2015年10月7日 下午3:36:02
*/
class Install_model extends ES_model{
 
/**
 * 开始生成model
 */
  public function init(){
    foreach($this->tables() as $t){
      echo $t.' <small style="color:#ccc">'.
           ($this->generate($t)?'success':'fail').
           '</small><br>';
    }
  }
/**
 * 获取当前数据库下的所有的表
 * @return array
 */
  private function tables(){
    $sql = 'SHOW TABLES';
    $tables = array();
    foreach($this->db->query($sql) as $t){
      $tables[] = $t->Tables_in_fcmayi;
    }
    return $tables;
  }
  
/**
 * 获取某表的所有字段
 * @param string $table
 * @return array
 */
  private function fields($table){
    $this->tableName = str_replace($this->prefix(), '', $table);
    $sql = "SHOW COLUMNS FROM `{$table}`";
    $fields = array();
    foreach($this->db->query($sql) as $f){
      $f->Key == 'PRI' && $this->primaryKey = $f->Field;
      $fields[] = $f->Field;
    }
    return $fields;
  }
  
/**
 * 表前缀
 */
  private function prefix(){
    global $configs;
    return $configs->database->prefix;
  }
  
/**
 * 生成model
 * @param string $table
 * @return bool
 */
  private function generate($table){
    $fields = $this->fields($table);
    $path = APPPATH."models/{$this->tableName}_model.php";
    $pkid = '';
$model = <<<PHP
<?php defined('APPPATH') OR exit('POWERED BY Enozoomstudio');
/**
 *
 * @author Joe
 * %s
 */
class %s_model extends ES_model{
  protected \$tableName = '{$this->tableName}';
  protected \$primaryKey = '{$this->primaryKey}';  
  public function _attributes(\$attr=''){
    \$atts = array(
%s
    );
    return empty(\$attr)?\$atts:(isset(\$atts[\$attr])?\$atts[\$attr]:FALSE);
  }
}
PHP;

$attrs = '';
foreach( $fields as $field )
$attrs .= <<<PHP
                  {$field} => '{$field}',

PHP;

    $model = sprintf( $model, get_format_time(), ucfirst($this->tableName), rtrim($attrs) );
    $n = file_put_contents($path, $model);
    return $n > 0;
  }
}