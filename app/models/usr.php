<?php namespace app\models;
/**
 * 数据模型
 * 用户model示例
 * @author ES3.0160219 
 * 2016-02-24 09:14:58
 */
class Usr extends Model{
  protected $tableName = 'usr';
  protected $primaryKey = 'usr_id';  
  public function _attributes($attr=''){
    $atts = [
              'usr_id' => '#',
              'usr_name' => '用户名',
              'usr_pword' => '密码',
              'usr_regtime' => '注册时间',
              'pic_id' => '头像',
              'usr_mobile' => '手机',
              'usr_lasttime' => '上次登录',
              'status_id' => '状态',
              'level_id' => '级别',
              'category_id' => '分类',
            ];
    // 插入关联参数
    foreach($this->__params_assign() as $param){
      $atts[$param->ok_key] = $param->ok_intro;
    }
    return empty($attr)?$atts:(isset($atts[$attr])?$atts[$attr]:FALSE);
  }
  
  public function __level_ids($id=0){
    return $this->__categories($id,12);
  }
  public function __category_ids($id=0){
    return $this->__categories($id,11);
  }
  
  
  /**
   * 判断用户是否能够登录
   * @param string $usr_name
   * @param string $usr_pword
   * @return int 如果成功在返回主键usr_id,否则返回0
   */
  public function _login($usr_name='',$usr_pword='',$category_id = '113'){
    if(in_array('', [$usr_name,$usr_pword])) return 0;
    $usr_pword = $this->generate_password($usr_pword);
    
    //登录条件，用户类别正确、状态正常、用户名密码匹配
    $where = "`category_id` = {$category_id} AND `status_id` = 411 AND `usr_name` = '".$this->db->_escape($usr_name)."' AND `usr_pword` = '{$usr_pword}'";
    $usr_id = empty($usr = $this->_get($where,'usr_id,status_id'))?0:$usr[0]->usr_id;
    empty($usr_id) || $this->_update($usr_id, ['usr_lasttime'=>time()]);
    return $usr_id;
  }
  /**
   * 用户注册
   * @param array $data
   * @return int 如果成功返回主键usr_id,否则返回0
   */
  public function _register($data=[]){
    // 如果用户名未设置但设置了手机号，则将手机号作为用户名
    empty($data['usr_name']) 
    && !empty($data['usr_mobile']) 
    && \es\core\isMobile($data['usr_mobile']) 
    && $data['usr_name'] = $data['usr_mobile'];
    
    // 必须存在必要参数，且不能为空
    foreach(['usr_name','usr_pword','category_id'] as $k){
      if(empty($data[$k]))return 0;
    }
    return $this->_insert($data);
  }
  
  public function _insert($array){
    empty($array['usr_pword']) && $array['usr_pword'] = DEF_PWORD;
    if(empty($array['usr_mobile'])){// 如果未设置手机号，但填写的用户名为手机号，则手机号=用户名
      $array['usr_mobile'] = \es\core\isMobile($array['usr_name'])?
                             $array['usr_name']:
                             substr(ceil(microtime(1)*10000),-11);// 默认当前时间的微秒数的后11位，以解决唯一约束
    }
    $array['usr_pword'] = $this->generate_password($array['usr_pword']);
    $array['usr_regtime'] = $array['usr_lasttime'] = time();
    return parent::_insert($array);
  }
  
  public function _mdy($pkid=0,$data=[],$pics=['pic_id']){
    if( empty( $data['usr_pword']) ) {
      unset( $data['usr_pword'] );
    }else{
      $data['usr_pword'] = $this->generate_password($data['usr_pword']);
    }
    
    return parent::_mdy($pkid,$data,$pics);
  }
}