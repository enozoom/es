<?php
namespace app\controllers\esweb;

use es\core\Controller\HtmlController;
final class Home extends HtmlController{
  public $title = '欢迎使用ES';
  public $keywords = 'ES框架,PHPES框架,CIES框架';
  
  public function index(){
    $this->view(['v'=>number_format(ES_VERSION,1)]);
  }
  
  /**
   * 数据库操作示例
   * 前台预览网址：http://localhost/esweb/home/demo
   */
  public function demo(){
    $this->load->model('Demo'，'D');
    // 获取所有行
    $data1 = $this->D->_get();
    $sql = $this->D->db->last_query();// 上一次数据库执行语句
    var_dump($sql,$data1);
    
    // 根据条件获取
    $where = 'demo_id < 3';
    $data2 = $this->D->_get($where);
    $sql = $this->D->db->last_query();
    var_dump($sql,$data2);
    
    // 只返回要求字段
    $select = 'demo_name';
    $data3 = $this->D->_get($where,$select);
    $sql = $this->D->db->last_query();
    var_dump($sql,$data3);
    
    // 按规则排序
    $orderby = 'demo_name desc';
    $data4 = $this->D->_get($where,$select,$orderby);
    $sql = $this->D->db->last_query();
    var_dump($sql,$data4);
    
    // 限制返回的范围
    $limit = array(1,1);
    $data5 = $this->D->_get($where,$select,$orderby,$limit);
    $sql = $this->D->db->last_query();
    var_dump($sql,$data5);
    
    $where = FALSE;
    $limit = array(2,2);
    $data6 = $this->D->_get($where,$select,$orderby,$limit);
    $sql = $this->D->db->last_query();
    var_dump($sql,$data6);
    
    // 根据主键获取某行
    $data7 = $this->D->_getByPKID(2);
    $sql = $this->D->db->last_query();
    var_dump($sql,$data7);
    
  }
}
?>