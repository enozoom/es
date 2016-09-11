<?php

namespace es\core\Toolkit;

trait Ary {
    /**
     * $keys中的值是否存在于$data的键中
     * @param array $keys
     * @param array $data
     */
    protected function isRequired($keys,$data=[]){
        empty($data) && $data = $_POST;
        foreach($keys as $k)
        {
            if( !key_exists($k, $data) )
            {
                return FALSE;
            }
        }
        return TRUE;
    }
    
    /**
     * 将一组对象中的某两个字段简化成一维键值数组
     * @param object $objs
     * @param string $k     要转成key的字段
     * @param string $v     要转成value的字段
     */
    protected function obj2kvArray($objs,$k,$v)
    {
        $ary = [];
        if(!empty($objs)){
            foreach($objs as $o)
            {
                if( isset($o->$k) && isset($o->$v) )
                {
                    $ary[$o->$k] = $o->$v;
                }
            }
        }
        return $ary;
    }
    
    /**
     * 对数据进行过滤
     * 场景：如$_POST提交的数据可能与数据库的数据相同(未变化)，此时不更改相同数据，仅更改非变化数据。
     * @param mix $post 要过滤的数组或独享
     * @param object $obj 数据库的原数据
     * @return mix 过滤后的数组或对象
     */
    protected function filterData($post,$obj){
        $return = [];
        $isAry = TRUE;
        if( is_object($post) ){
            $isAry = FALSE;
            $post = (array)$post;
        }
        foreach( $post as $k=>$v ){
            !empty($v) && isset($obj->$k) && $v != $obj->$k && $return[$k] = $v;
        }
        return $isAry?$post:(object)$post;
    }
    
}

?>