<?php
namespace es\helpers;

if( !function_exists('cls_name') ){
/**
  * 获取含有命名空间的类的名字
  * @param mix $obj
  * @return string
  */
  function cls_name($obj,$complex=FALSE){
    $cls = get_class($obj);
    $cls = substr($cls, strrpos($cls, '\\')+1 );
    return $complex?strtolower($cls.'s'):$cls;
  }
}

if(!function_exists('categor_ies')){
/**
 * 数据表中的字段 与category有关，返回相关数据列表还是单个值
 * @param array $data
 * @param array $kv
 * @param number $id
 * @return []|string
 */
  function categor_ies($data=[],$id=0){
    if(empty($id)) return $data;
    foreach ($data as $d){
      if( $d->category_id == $id ) return $d->category_name ;
    }
    return '';
  }
}
