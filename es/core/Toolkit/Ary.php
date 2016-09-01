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
    
}

?>