<?php

namespace app\controllers\esadmin;

trait TraitId
{
    
    /**
     * 处理数据。
     * 本方法会在insert,update之前执行，将数据转成合适数据以便正常sql
     * @param array $data
     * @return array $data
     */
    abstract protected function dataValidate(Array $data);
    /**
     * 新建和修改的默认方法
     * @param number $id
     */
    abstract protected function id($id=0);
    
    /**
     * 对提交的数据进行基本的过滤或转化
     * 方法前置于dataValidate()处理数据之前。
     * @param array $data
     */
    private function preHook(Array $data)
    {
        $ks = ['category_pid','category_id','goods_category_id'];
        foreach( $ks as $k )if(key_exists($k, $data))
        {
            if(is_array($data[$k]))
            {
                $len = count($data[$k]);
                if($len > 1)
                {
                    for( $i=0;$i<$len;$i++ )
                    {
                        if(empty($data[$k][$i])){
                            unset($data[$k][$i]);
                        }
                    }
                    $data[$k] = array_pop($data[$k]);
                }else{
                    $data[$k] = $data[$k][0];
                }
            }
        }
        return $data;
    }
    
    /**
     * 新增一条|修改一条|查询一条记录
     * @param number $id
     * @param \es\core\Model\Model $model
     * @param array $require_names
     */
    protected function _id($id=0,\es\core\Model\ModelAbstract $model = null,Array $require_names =[])
    {
        if( $this->isPost() ){
            $data = $this->dataValidate( $this->preHook($_POST) );
            //$this->getConfigs('logger')->debug($data);
            $flag = 1;
            if(empty($id)){// 新增
                $id = $model->_insert( $data );
            }else{// 修改
                $flag = $model->_update( $id,$data );
            }
            die( json_encode( ['err'=>$id&&$flag?0:1,'id'=>$id] ));
        }
        empty($this->Form) && $this->load->library('Html\\Form','Form');
        return empty($obj = $model->_getByPKID($id))?null:$obj;
    }
    
    /**
     * 上传
     * 对upload()的重载参考app\controllers\esadmin\Article
     * @param string $filename  input的name值
     * @param string $dir       上传到的文件夹
     * @param array $thumbnail  缩略图
     * @param string $print     直接打印
     * 
     */
    public function upload($filename='file',$dir='',$thumbnail=[],$print=TRUE){
        $this->load->library('Html\\Upload','Upload');
        $dir = 'uploads/'.(empty($dir)?$filename:$dir);
        $result =  $this->Upload->initialize(['thumb'=>$thumbnail,'upload_dir'=>$dir,'iptname'=>$filename]);
        $res = [ $filename=>[ ['url'=>$result['url'],'thumbnailUrl'=>$result['thumb']] ] ];
        if( !$print ) return $result;
        die( json_encode($res) );
    }
    
    
    /**
     * Select级联伴侣
     * 分类相关的JS级联调用，仅适用于Category表
     * 配合\es\libraries\Form->select()
     * 配合./app/data/js/esadmin.form.js
     * 生成的select中必要属性,如果select需要级联且自动加载，必须使用本方法。
     * 这方法有点鸡肋
     * 
     * @param object $m        含有category表外键的某表实例化对象
     * @param string $catField 外键 
     * @param bool   $self     返回的id串中是否包含查询使用的id
     * @return []
     */
    protected function _cat_select_pids($m,$catField='category_id',$self=FALSE){
        return ['data-pids'=>empty($m)?'':$this->C->_parentsIds($m->$catField,$self)];
    }
    
    /**
     * Form input默认值伴侣
     * @param object $m
     * @param string $name
     * @param string $dafault
     */
    protected function _defaultVal($m,$name,$dafault='0')
    {
        return ['value'=>empty($m)?$dafault:$m->{$name}];
    }
}

?>