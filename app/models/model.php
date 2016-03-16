<?php
namespace app\models;
use es\core\Controller;
/**
 * 数据交互时部分数据需要转换，如分类，状态，图片等等
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年1月22日下午4:59:21
 * ----------------------------------
 * 2016年2月26日08:50:09
 * 增加对关联表的支持
 */
class Model extends \es\core\Model{
  public function __status_ids($id=0){
    return $this->__categories($id,41);
  }
//----------------------------------------------------------------------------------
// 关联参数相关
//----------------------------------------------------------------------------------

/**
 * 将提交中含有关联表的数据进行剔除，并返回剔除数据的数组
 * @param array $data
 * @param bool  $filter_empty 是否过滤掉空值
 * @return array [[ov_value=>,ok_id=>],]
 */
  public function __params_filter(&$data,$filter_empty = TRUE){
    $params_data = [];
    foreach($this->__params_assign() as $param){
      if( isset($data[$param->ok_key]) ){
           $k = $data[$param->ok_key];
          unset($data[$param->ok_key]);
        if( !$filter_empty || !empty($k)){// 过滤冗余数据，当关联的字段中无值则不插入数据
          $params_data[] = ['ov_value'=>$k,'ok_id'=>$param->ok_id];
        }
      }
    }
    return $params_data;
  }
  
/**
 * 获取当前表的关联参数字段
 * 2016年2月26日08:50:24
 * @return [obj,..]
 */
  public function __params_assign(){
    return $this->db->_get('option_key',"ok_fktable = '{$this->tableName}'");
  }
  
/**
 * 获取当前表的关联参数字段的值
 * 2016年2月26日08:50:31
 * @param int $fkid_id 关联的外表数据行主键ID
 * @return array
 */
  public function __params_value($fkid_id = 0){
    $fkdata = $this->__params_value();
    $data = [];
    foreach($fkdata as $ok){
      $where = "fkid_id = {$fkid_id} AND ok_id = {$ok->ok_id}";
      $ov = $this->db->_get('option_value',$where,'ov_value',FALSE,1);
      empty($ov) || $data[$ok->ok_key] = $ov->ov_value;
    }
    return $data;
  }

//----------------------------------------------------------------------------------
// 常规方法
//----------------------------------------------------------------------------------
/**
 * 获取多行数据
 * @param \es\core\Model $model 
 * @param string $where
 * @param string $select
 * @param string $orderby
 * @param array $limit
 * @param array $pics            和pic表关联的字段且需要转换的，不需要转留空
 * @param array $categories      和category表关联的字段且需要转换的，不需要转留空
 */
  public function _rows($where='',$select='*',$orderby='',$limit=[],$pics=['pic_id'],$categories=['category_id']){
    empty($where) && empty($limit) && $limit = [10];
    $objs = [];$pk = $this->primaryKey;
    $ids = $this->_get($where,$pk,$orderby,$limit);
    foreach($ids as $id){
      $objs[] = $this->_row($id->$pk,$select,$pics,$categories);
    }
    return $objs;
  }
  
/**
 * 获取单行数据
 * @param number $pkid
 * @param string $select
 * @param array $pics            和pic表pic_id关联的字段且需要转换为汉字，不需要转留空
 * @param array $categories      @todo和category表关联的字段且需要转换为汉字，不需要转留空
 * @return obj|null
 */
  public function _row($pkid=0,$select=FALSE,$pics=['pic_id'],$categories=['category_id']){
    $select_fk = []; 
    $select_tb = explode(',',$select);
    // 过滤select中的关联参数字段
    if(!empty($fks  = $this->__params_assign())){
      if(!empty($select)){
        foreach($fks as $fk){
          if( in_array($fk->ok_key,$select_tb) ){
            $select_fk[$fk->ok_id] = $fk->ok_key;
          }
        }
      }else{
        foreach ($fks as $fk) $select_fk[$fk->ok_id] = $fk->ok_key;
      }
    }
    empty($select_fk) || $select_tb = array_diff($select_tb, $select_fk);
    $obj = $this->_get_by_PKID($pkid,implode(',', $select_tb));
    if(empty($obj)) return null;
    
    if(!empty($select_fk)){// 加入关联字段
      foreach($select_fk as $k=>$v){
        $where = "ok_id = {$k} AND fkid_id = {$pkid}";
        $o = $this->db->_get('option_value',$where,'ov_value',FALSE,1);
        //empty($o) || $obj->$v = $o[0]->ov_value;
        $obj->$v = empty($o)?'':$o[0]->ov_value;
      }
    }
    foreach($pics as $pic){// 将图片ID转为图片url
      if(!empty($obj->$pic)){
        $pic_url = '';
        foreach( explode(',', $obj->$pic) as $pic_id ){
          $pic_url .= ','.$this->pic_id2url($pic_id);
        }
        $obj->$pic = empty($pic_url)?'':substr($pic_url,1);
      }
    }
    
    foreach($categories as $cat){// 将分类ID转为分类名
      if( !empty( $obj->$cat ) ){
        $c = $this->db->_get('category',"category_id = ".$obj->$cat,'category_name',FALSE,1);
        if( !empty($c) ){
          $obj->$cat = $c[0]->category_name;
        }
      }
    }
    
    return $obj;
  }

/**
 * 新增一条数据
 * @param \es\core\Model $model
 * @param array $data
 * @param array $pics            含图片地址的数组keys
 * @return int pkid
 */
  public function _add($data=[],$pics=['pic_id']){
    $params = $this->__params_filter($data);
    $pkid = $this->_insert($this->_data($data,$pics));
    if(!empty($pkid)){
      foreach ($params as $param){
        $ov_id = $this->db->_insert($param+['fkid_id'=>$pkid],'option_value');
      }
    }
    return $pkid;
  }
  
/**
 * 修改一条数据
 * @param \es\core\Model $model
 * @param number $pkid
 * @param array $data
 * @param array $pics
 * @return int pkid
 */
  public function _mdy($pkid=0,$data=[],$pics=['pic_id']){
    $params = $this->__params_filter($data,FALSE);
    $this->trans_start();// 事务开始
    
      $pkid = $this->_update($pkid,$this->_data($data,$pics));
      $ovid = empty($params)?1:0;
      if(!empty($pkid)){
        $i = 0;
        foreach ($params as $param){// 关联参数修改
          $where = "fkid_id = {$pkid} AND ok_id = {$param['ok_id']}";
          $ov = $this->db->_get('option_value',$where,'ov_id',FALSE,1);
          if(!empty($ov)){
            $f = $this->db->_update($ov[0]->ov_id,['ov_value'=>$param['ov_value']],'ov_id','option_value');
            if(empty($f)){
              break;
            }
          }else{// 可能是在修改页面新增的关联参数
            $f = $this->db->_insert($param+['fkid_id'=>$pkid],'option_value');
            if(empty($f)){
              break;
            }
          }
          $i++;
        }
        count($params) == $i && $ovid = 1;
      }
      $result = !empty($pkid) && !empty($ovid);
      if(!$result){
        $this->rollback();// 事务回滚
        $pkid = 0;
      }
    
    $this->trans_end();// 事务提交结束
    
    return $pkid;
  }
  
/**
 * 删除一条数据，并删除相关参数，图片等。
 * @param int $pkid
 * @return bool 
 */
  public function del($pkid,$pics=['pic_id']){
    $obj =  $this->_get_by_PKID($pkid);
    $r = 0;
    if(!empty($obj)){$this->trans_start();
      $ovflag = $objflag = $i = 0;
      // 删除关联参数
      $params = $this->__params_assign();
      empty($params) && $ovflag = 1;//无关联表
      
      foreach( $params as $param ){
        $where = sprintf('ok_id = %d AND fkid_id = %d',$param->ok_id,$pkid);
        $f = $this->db->delete($where,'option_value');
        if(!$f){
          break;
        }
      $i++;} count($params) == $i && $ovflag = 1;
    
      // 删除主表内容
      $where = '`%s` = %d';
      $objflag = $this->db->delete(sprintf($where,$this->primaryKey,$pkid),$this->tableName);
      
      $ovflag && $objflag && $r = 1;
      !$r && $this->rollback();
      if($r){
        // 删除图片
        foreach( $pics as $pic ){
          if(!empty($pic_id = $obj->$pic)){
            $file = $this->pic_id2url($pic_id);
            // 从数据库删除
            $f = $this->db->delete(sprintf($where,'pic_id',$pic_id),'pic');
            if($f){// 删除图片文件
              if(!empty($file = strstr($file, '/uploads'))){
                if( file_exists('.'.$file) ){
                  unlink('.'.$file);
                }
              }
            }
          }
        }
      }
      
    $this->trans_end();}
    return $r;
  }
  
/**
 * 对数据进行整合，将图片路径转成图片ID
 * @param \es\core\Model $model
 * @param array $data
 * @param array $pics            含图片地址的数组keys
 * @return int pkid
 */
  public function _data($data=[],$pics=['pic_id']){
    if(!empty($pics)){
      $pic_urls = '';
      foreach($pics as $pic){
        empty($data[$pic]) || $pic_urls .= ','.$data[$pic];
        unset($data[$pic]);
      }
      
      if(!empty($pic_urls)){
        $pic_ids = $this->pic_url2id( substr($pic_urls, 1) );
        array_key_exists('pic_id', $this->_attributes() ) && $data['pic_id'] = $pic_ids;
        array_key_exists('pic_ids', $this->_attributes() ) && $data['pic_ids'] = $pic_ids;
      }
    }
    return $data;
  }
  
/**
 * 将图片ID兑成图片地址
 * @param int $pic_id
 * @return string
 */
  public function pic_id2url($pic_id=0){
    $pic = $this->db->_get_by_PKID($pic_id,'pic_id','pic','pic_url');
    return empty($pic)?'':$pic->pic_url;
  }
  
/**
 * 将图片地址兑成ID
 * 1.保存到pic表，2.将返回值返回
 * @todo 需要对图片进行裁剪 2016年1月25日11:56:05 Joe 将图片修正成为符合前台要去的图。
 * 
 * @param string $urls 多个url,用','连接
 * return string 多个id用','连接返回。
 */
  public function pic_url2id($urls='',$category_id=4){
    $pic_ids = '';
    foreach( explode(',', $urls) as $url){
      // 查询图片是否已经存在，存在则不再存储

        $data = ['pic_timestamp'=>time(),'category_id'=>$category_id,'pic_url'=>$url];
        if($pkid = $this->db->_insert($data,'pic')){
          $pic_ids .= ','.$pkid;
        }
      
    }
    return empty($pic_ids)?'':substr($pic_ids, 1);
  }
  
//----------------------------------------------------------------------------------------
// 为from datagrid提供的辅助方法
// Joe
// 2016年3月12日09:13:08
//----------------------------------------------------------------------------------------
/**
 * 为form,datagird自动调用的'__(filed)s'方法提供数据
 * @param int $id
 * @param int $category_id
 * 
 * @param array $table_fields  ['table'=>'' ,'fields'=>',' ,'where'=''] 
 * 以满足多表数据的封装，不仅仅局限于category表
 */
  protected function __categories($id,$category_id=-1,$table_fields=[]){
    // 默认为category表
    empty($table_fields) && $table_fields = ['table'=>'category','fields'=>'category_id,category_name'];
    $where = '';
    // 使用MODEL：category的方法
    if( $table_fields['table'] == 'category' ){
      $ES = Controller::get_instance();
      if( empty($ES->cat) ){
        $ES->load->model('category','cat');
      }
      $ids = '';
      is_array($category_id) || $category_id = [$category_id];
      foreach($category_id as $cid){
        $cid > 0 && $ids .= $ES->cat->_children_ids($cid,FALSE,empty($id)?FALSE:TRUE).',';
      }
      $where = empty($ids)?FALSE:'category_id in ('.substr($ids,0,-1).')';
    }else{
      $where = empty($table_fields['where'])?'':$table_fields['where'];
    }
    $tmps = $this->db->_get($table_fields['table'],$where,$table_fields['fields']);
    return $this->__to_categories( \es\helpers\categor_ies($tmps,$id,explode(',', $table_fields['fields'])), 
                                   explode(',', $table_fields['fields']));
  }
  
/**
 * 将其他数据集转成分类集
 * 如[obj(brand_id=>,brand_name)] => [obj(category_id=>,category_name)]
 * @param array $data
 * @param array $kv   被转换的键
 * @return array
 */
  protected function __to_categories($data=[],$kv=[]){
    if( is_array( $data ) )
    foreach ($data as &$d){
      if( !empty(array_diff($kv, [ 'category_id', 'category_name' ])) ){
        $d->category_id = $d->{$kv[0]};unset($d->{$kv[0]});
        $d->category_name = $d->{$kv[1]};unset($d->{$kv[1]});
      }
    }
    return $data;
  }
}