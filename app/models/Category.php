<?php
namespace app\models;

use es\core\Model\ModelAbstract;
use es\core\Toolkit\AryTrait;
class Category extends ModelAbstract
{
    use AryTrait;
    public function _attributes($attr = '')
    {
        $attrs = [
            'category_id'=>'#',
            'category_title'=>'分类名',
            'category_pid'=>'父ID',
            'category_sequence'=>'排序'
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
    
    /**
     * 将idName()简化成一维数组
     * @param number $category_pid
     * @return [category_id:category_title,..]
     */
    public function simpleIdName($category_pid=0)
    {
        return $this->obj2kvArray($this->idName($category_pid), 'category_id', 'category_title');
    }
    
    public function __category_pids($id=0,$pid=0)
    {
        return 
        (empty($id)?$this->simpleIdName($pid):$this->_getByPKID($id,'category_id,category_title')->category_title);
    }
    /**
     * 根据ID递归所有上级，并拼接返回
     * @param number $category_id
     * @param string $hasself     是否拼接自己
     * @param string $recursion   是否递归
     * @param string $glue        拼接字符串
     */
    public function _parentsIds($category_id=0,$hasself=TRUE,$recursion=TRUE,$glue='-'){
        $cats = $this->_parentsIdsHelper($category_id,$recursion,$glue);
        $cats = explode($glue, $cats);
        empty($hasself) || array_unshift($cats,$category_id);
        //array_pop($cats);// 最后一个为父级为0
        $cats = array_reverse($cats);
        return implode($glue, $cats);
    }
    /**
     * _parents_ids()的辅助函数
     * @param number $category_id
     * @param string $hasself
     * @param string $recursion
     * @param string $glue
     * @return string
     */
    private function _parentsIdsHelper($category_id=0,$recursion=TRUE,$glue='-'){
        $cat = $this->_getByPKID($category_id,'category_pid,category_id');
        $catid = $cat->category_pid;
        if($recursion && !empty($catid)){
            $catid .= $glue.$this->_parentsIdsHelper($catid,$recursion,$glue);
        }
        return $catid;
    }
    
    public function _update($pkid,Array $array){
        if(isset($array['category_pid']) && $pkid == $array['category_pid']){
            unset($array['category_pid']);
        }
        return parent::_update($pkid, $array);
    }
}
