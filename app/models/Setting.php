<?php
namespace app\models;
/**
 * 未放置于数据库中。
 */
use es\core\Model\ModelAbstract;
use es\core\Toolkit\FileStatic;
final class Setting extends ModelAbstract
{
    private $data = null;
    private $path = 'data/setting.json';
    public function __construct(){
      file_exists(APPPATH.$this->path) && $this->data = json_decode(file_get_contents(APPPATH.$this->path));
    }
    public function _attributes($attr = '')
    {
        $attrs = [
                    'index_title'=>'网站标题',
                    'index_keywords'=>'网站关键词',
                    'index_description'=>'网站描述',
                    'website_open'=>'网站开关',
                    'website_close_msg'=>'闭站阶段的提示',
        //            'a_attr'=>'新增一条属性',
                 ];
        return isset($attrs[$attr])?$attrs[$attr]:$attrs;
    }
    
    public function insert(Array $data){
        return $this->_insert($data);
    }
    
    public function get($select=''){
        return $this->_get('',$select);
    }
    
    public function getByPKID($key){
        return $this->_getByPKID($key);
    }
    
    public function update(Array $data){
        return $this->_update('', $data);
    }
    
//-----------------
// 重写了基本方法
//-----------------
    public function _insert(Array $array){
        $flag = FALSE;
        if( empty($this->data) ){
            $this->data = new \stdClass();
            foreach($array as $k=>$v){
                $this->data->$k = $v;
                $flag = TRUE;
            }
        }
        if($flag){
            $i = strrpos($this->path, '/');
            $path = substr($this->path, 0,$i);
            $file = substr($this->path, $i+1);
            $flag = file_put_contents(FileStatic::mkdir(APPPATH.$path,FALSE).$file, 
                json_encode($this->data,JSON_UNESCAPED_UNICODE))>0;
        }
        return $flag;
    }
    
    public function _update($pkid,Array $array){
        $flag = FALSE;
        foreach($array as $k=>$v){
            if( !empty($this->data) ){
                $this->data->$k = $v;
                $flag = TRUE;
            }
        }
        if($flag){
            $flag = file_put_contents(APPPATH.$this->path, json_encode($this->data,JSON_UNESCAPED_UNICODE))>0;
        }
        return $flag;
    }
    
    public function _get($where='', $select='*', $orderby='',Array $limit=[]){
        $data = null;
        if(!empty($this->data)){
            if(!empty($select)){
                $data = new \stdClass();
                foreach( explode(',', $select) as $k ){
                    $data->$k = empty($this->data->$k)?'':$this->data->$k;
                }
            }else{
                $data = $this->data;
            }
        }
        return $data;
    }
    
    public function _getByPKID($id='',$select='*'){
        return empty($v=$this->_get($id))?'':$v[$id];
    }
}

