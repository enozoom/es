<?php

namespace app\controllers\esadmin;

final class Category extends AbstractEsadmin
{
    use TraitId,TraitList;

    public function lists($category_pid=0,$per=10,$offset=0)
    {
        $where = "category_pid = {$category_pid}";
        $select = 'category_id,category_title,category_pid,category_sequence';
        
        $rows = $this->_lists($this->C,['where'=>$where,'select'=>$select],
                              'esadmin/category/lists/'.$category_pid,$per,$offset);
        echo $this->Datagrid->init($rows,$this->C)->display().
             '<p class="load-script" data-src="/min/esadmin.category.js"></p>';
    }
    
    public function id($id=0)
    {
        $m = $this->_id($id,$this->C);
        $catpids = ['data-pids'=>''];
        if(empty($m)){
            if(!empty($_GET) && !empty($_GET['pid'])){
               $catpids['data-pids'] = $this->C->_parentsIds($_GET['pid'],TRUE);
            }
        }else{
            $catpids = $this->_cat_select_pids($m,'category_id',TRUE);
        }
        
        $this->Form->init($this->C,$m)
             ->input('category_title')
             ->select('category_pid',$catpids)
             ->input('category_sequence',['value'=>empty($m)?1:$m->category_sequence])
             ->display(['data-selectauto'=>1]);
    }
    protected function dataValidate(Array $data)
    {
        return $data;
    }
    public function del($id=0){
        $r = ['err'=>1];
        if( $id && !$this->C->_getTotalnum("category_pid = {$id}") )
        {
            if( $this->C->_delete("category_id = {$id}") )
            {
                $r['err'] = 0;
            }
        }
        die( json_encode($r) );
    }
    
    public function catsbypid($pid=0){
        $cats = $this->C->simpleIdName($pid);
        $this->httpMime('json',TRUE);
        die( json_encode(['err'=>empty($cats),'msg'=>$cats]) );
    }
}