<?php
namespace app\controllers\esadmin;

trait TraitList
{
    /**
     * 获取多条数据
     * @param \es\core\Model\Model $model
     * @param array  $sql [select=>'',where=>,orderby=>]
     * @param string $url 分页所需的url
     * @param number $per 分页，行数
     * @param number $offset 分页，偏移行数
     * @param bool   $cache 是否启用缓存
     * @return ['model_names'=>rows,'pagination'=>htmlstring]
     */
    protected function _lists(\es\core\Model\ModelAbstract $model = null,
                              Array $sql=['select'=>'','where'=>'','orderby'=>''],
                              $url,$per=10,$offset=0,$cache=false){
        
        empty($this->Pagination) && $this->load->library('Html\\Pagination');
        empty($this->Datagrid) && $this->load->library('Html\\Datagrid');
        
        foreach(['select','where','orderby'] as $k) isset($sql[$k]) || $sql[$k] = '';
        $total = $model->_getTotalnum($sql['where']);
        $holder = 'A';
        $pagination = '';
        if($total>$per){
            $pagination = $this->Pagination->init($total,$per,$offset,1,$holder);
            empty($pagination)|| $pagination = str_replace($holder, $url, $pagination);
        }
        $data = $model->_get($sql['where'],$sql['select'],$sql['orderby'],[$per,$offset]);
        $cls = $this->Datagrid->clsName($model);
        
        // 可能存在查询，需要使用AbstractEsadmin的_search_pagination方法
        if( !empty($pagination) ){
            $pagination = preg_replace('@data-href="([^"]+)@', 
                                       $this->_search_pagination('data-href="$1'),
                                       $pagination);
        }
        return ['pagination'=>$pagination,$cls=>$data];
    }
    
    /**
     * 删除操作
     * @param int $id
     */
    abstract public function del($id=0);
    abstract public function lists($category_id=0,$per=10,$offset=0);
}

?>