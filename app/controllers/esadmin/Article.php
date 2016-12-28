<?php

namespace app\controllers\esadmin;

use es\core\Toolkit\StrStatic;

final class Article extends AbstractEsadmin
{
    use TraitId, TraitList{
        TraitId::upload as _upload;
    }
    public function __construct(){
        parent::__construct();
        $this->load->model('Article','A');
    }
    
    public function lists($category_id=0,$status=0,$per=10,$offset=0){
        $where = empty($category_id)?'':"category_id = {$category_id}";
        $where = (empty($where)?'':' AND ').(empty($status)?"status_id <> 23":"status_id = {$status}");
        $where = $this->_search_where($where, 'article_id = \'%s\' OR article_title like \'%%s%\'');
        
        $select = 'article_id,article_title,article_timestamp,category_id,status_id';
        $rows = $this->_lists($this->A,['where'=>$where,'select'=>$select],
                        'esadmin/article/lists/'.$category_id.'/'.$status,$per,$offset);
        echo $this->Datagrid->init($rows,$this->A)->display().
             '<p class="load-script" data-src="/min/esadmin.article.js"></p>';
    }
    public function id($id=0){
        $m = $this->_id($id,$this->A);
        $this->Form->init($this->A,$m)
        ->input('article_title')
        ->input('article_keywords')
        ->input('article_description')
        ->file('article_pic',['help'=>'图片尺寸要要求[宽265px，高183px],<small>当作为logo图片时，尺寸为[宽220-420px,高80px]</small>'])

        ->textarea('article_txt')
        ->select('category_id',$this->_cat_select_pids($m,'category_id',TRUE))
        ->select('status_id')
        
        ->input('article_author')
        ->input('article_timestamp')
        ->select('mode_id')
        ->display();
    }
    
    public function upload($filename='file',$dir='article',$thumbnail=[200,200],$print=TRUE){
        return $this->_upload($filename,$dir,$thumbnail,$print);
    }
    
    /**
     * @param number $id
     */
    public function del($id=0){
        $r = ['err'=>1,'msg'=>''];
        $article = $this->A->_getByPKID($id,'category_id');
        if( !empty($article) ){
            if( strpos('0-'.$this->C->_parentsIds($article->category_id), '-5-')===FALSE  ){// 系统文章不能删除
                $r['err'] = 0;
            }else{
                $r['msg'] = '系统文章禁止删除';
            }
        }
        if(!$r['err']){
            $this->A->_update($id,['status_id'=>23]);
            //$this->A->_delete("article_id = {$id}");
        }
        die( json_encode($r) );
    }
    protected function dataValidate(Array $data){
        $data['article_pic'] = $data['article_pic-tmp'];
        $data['article_timestamp'] = strtotime($data['article_timestamp']);
        //$data['article_txt'] = StrStatic::cleanStyleAndScript($data['article_txt']);
        return $data;
    }
}
