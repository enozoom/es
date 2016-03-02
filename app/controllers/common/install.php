<?php
namespace app\controllers\common;
use es\core\Controller;
/**
* 
* @author Joe e@enozoom.com
* 2015年10月7日 下午3:34:40
*/
class Install extends \es\core\Controller{
  
// 生成数据库表结构，以及网站基本数据
  public function index(){
    $this->load->helper('html');
    $this->load->view('install/step');
  }
  
  public function model(){
    $this->load->model('install','i');
    $this->i->init();
  }
  
  public function t($prefix='eno_'){
  }
  
  public function ajax($step=0){
    $r = ['err'=>1,'msg'=>'请求错误！'];
    $debug = 0;
    /*
    $_POST = ['dbhost'=>'localhost',
             'dbpfix'=>'es_',
             'dbport'=>'3306',
             'dbpwrd'=>'','dbname'=>'demo',
             'dbuser'=>'root'];
    */
    
    if( $debug || (!empty($step) && $this->_is_post()) ){
      try {
        extract($_POST);
        switch ($step){
          case 1:// 配置数据库文件
            if(in_array('', [$dbhost,$dbport,$dbuser,$dbname])){//dbpwrd//dbpfix
              $r['msg'] = '数据库地址,数据库端口,数据库用户,数据库名称均不能为空。';
            }else{
              // 数据库连接
              $mysqli =@new \mysqli($dbhost,$dbuser,$dbpwrd,'',$dbport);
              if ($mysqli->connect_errno) {
                $r['msg'] = "数据库连接失败 {$dbuser}@{$dbhost}:{$dbport}。";
              }else{
                if(!$mysqli->select_db($dbname)){
                  $r['msg'] = "数据库{$dbname}不存在！";
                }else{
                  //写入配置文件
                  $w1 = $this->init_databse_eno($dbhost,$dbuser,$dbpwrd,$dbname,$dbport,$dbpfix);
                  if($w1['err']){
                    $r = $w1;
                  }else{
                    // 执行sql文件
                    $r = $this->init_sql_file($dbpfix);
                  }
                }
              }
            }
          break;case 2:// 设定管理员
            $this->load->model('usr');
            $usr_id = $this->usr->_register(['usr_name'=>$usrname,'usr_pword'=>$usrpwrd,'category_id'=>111]);
            if( $usr_id ){
              $r['err'] = 0;
            }else{
              $r['msg'] = '创建管理员失败。';
            }
          break;case 3:// 设置网站基本信息
            if($webtit == ''){
              $r['msg'] = '网站标题不能为空。';
            }else{
              $this->load->model('article');
              $article_id = $this->article->_update(1,['article_title'=>$webtit,'article_keywords'=>$webkey,'article_description'=>$webdes]);
              if( $article_id ){
                $r['err'] = 0;
              }else{
                $r['msg'] = '设置网站基本信息失败。';
              }
            }
          break;
        }
      } catch (\Exception $e) {
        die( json_encode($r) );
      }
    }
    die( json_encode($r) );
  }
  
/**
 * 配置数据库文件
 */
  private function init_sql_file($prefix='es_'){
    $this->load->model('install','i');
    $sql = file_get_contents(APPPATH.'data/install/install.sql');
    // 去注释
    $sql = preg_replace(['~#.*\s*~','~\s*\n~',],['',''], $sql);
    $sqls = explode(';', $sql);
    for($i=1;$i<count($sqls);$i++){
      if(!empty($sql = $sqls[$i])){
        $sql = str_replace('`es_', '`'.$prefix, $sql);
        $this->i->db->query($sql);
      }
    }
    return ['err'=>0];
  }

/**
 * 创建数据库表结构
 */
  private function init_databse_eno($dbhost,$dbuser,$dbpwrd,$dbname,$dbport,$dbpfix){
    // 写入配置文件
    $f = './configs/database.eno';
    if(!is_writable($f)){
      return ['msg'=>'请保证./configs文件夹下的database.eno具有可写权限','err'=>1];
    }
    $database_eno = <<<ENO
{
#---------
# 数据库
#---------
    
#数据库驱动的类名(类名和文件名一致，区别在于大小写)
"driver":"\\\\ES\\\\database\\\\ES_Mysqli",
    
# 数据库地址
"host":"{$dbhost}",
    
# 数据库用户名
"user":"{$dbuser}",
    
# 数据库密码
"password":"{$dbpwrd}",
    
# 数据库名
"dbname":"{$dbname}",
    
# 数据库端口
"port":"{$dbport}",
    
# 表前缀
"prefix":"{$dbpfix}",
}
ENO;
    $len = file_put_contents($f, $database_eno);
    return $len>0?['err'=>0]:['err'=>1,'msg'=>'写入'.$f.'失败。'];
  }

}