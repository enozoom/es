<?php defined('SYSPATH') OR exit('POWERED BY Enozoomstudio');
/**
 * 
 * @author Joe e@enozoom.com
 * 2015年6月24日下午6:24:38
 *
 */
class Home extends Html_controller{
  public $title = '欢迎使用ES';
  public function index(){
    $this->view();
  }
  
  /**
   * 数据库操作示例
   * 前台预览网址：http://localhost/public/home/demo
   */
  public function demo(){
    $this->load->model('demo_model','demo');
    // 获取所有行
    $data1 = $this->demo->_get();
    $sql = $this->demo->db->last_query();// 上一次数据库执行语句
    var_dump($sql,$data1);
    
    // 根据条件获取
    $where = 'demo_id < 3';
    $data2 = $this->demo->_get($where);
    $sql = $this->demo->db->last_query();
    var_dump($sql,$data2);
    
    // 只返回要求字段
    $select = 'demo_name';
    $data3 = $this->demo->_get($where,$select);
    $sql = $this->demo->db->last_query();
    var_dump($sql,$data3);
    
    // 按规则排序
    $orderby = 'demo_name desc';
    $data4 = $this->demo->_get($where,$select,$orderby);
    $sql = $this->demo->db->last_query();
    var_dump($sql,$data4);
    
    // 限制返回的范围
    $limit = array(1,1);
    $data5 = $this->demo->_get($where,$select,$orderby,$limit);
    $sql = $this->demo->db->last_query();
    var_dump($sql,$data5);
    
    $where = FALSE;
    $limit = array(2,2);
    $data6 = $this->demo->_get($where,$select,$orderby,$limit);
    $sql = $this->demo->db->last_query();
    var_dump($sql,$data6);
    
    // 根据主键获取某行
    $data7 = $this->demo->_get_by_PKID(2);
    $sql = $this->demo->db->last_query();
    var_dump($sql,$data7);
    
  }
}
?>