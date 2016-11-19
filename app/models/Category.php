<?php
namespace app\models;

use es\core\Model\ModelAbstract;
use es\core\Toolkit\AuthTrait;
use es\core\Toolkit\AryTrait;
class Category extends ModelAbstract
{
    use AuthTrait,AryTrait;
    public function _attributes($attr = '')
    {
        $attrs = [
            'category_id'=>'#',
            'category_title'=>'分类名',
            'category_pid'=>'父ID',
            'category_etc'=>'分类说明',
            'category_sequence'=>'排序',
            'category_timestamp'=>'创建时间'
        ];
        return empty($attr)?$attrs:(isset($attrs[$attr])?$attrs[$attr]:$attrs);
    }
    
    /**
     * 获取某父类下的所有子类category_id拼成的字符串
     * @param number  $category_pid  父类ID
     * @param string  $hasself       是否和父类ID一起返回
     * @param bool    $recursion     是否递归全部子类
     * @return string 'category_id,..'
     */
    public function _childrenIds($category_pid=0,$hasself=TRUE,$recursion=TRUE){
        $cats = $this->obj2kvArray( $this->_children($category_pid,$recursion),'category_id','category_title' );
        return empty($cats)?
        ( $hasself?$category_pid:-1 ):
        ( implode(',', array_keys($cats)).($hasself?','.$category_pid:'') );
    }
    
    /**
     * 获取某父类下的子类
     * @param int $category_pid
     * @param bool $recursion 是否递归全部子类
     * @return [obj(category_id=>,category_title=>),..]
     */
    public function _children($category_pid,$recursion=TRUE){
        $cats = $this->idName($category_pid);
        if($recursion && !empty($cats)){
            foreach ($cats as $cat){
                $cats = array_merge($cats,$this->_children($cat->category_id));
            }
        }
        return $cats;
    }
    /**
     * 根据父级id仅获取id或name字段
     * @param number $category_pid
     * @return [{category_id=>'',category_name=>''},..]
     */
    public function idName($category_pid=0)
    {
        return $this->_get("category_pid = {$category_pid}",'category_id,category_title','category_sequence DESC');
    }
}
