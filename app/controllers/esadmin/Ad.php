<?php

namespace app\controllers\esadmin;

final class Ad extends AbstractEsadmin
{
    
    use TraitId, TraitList{
        TraitId::upload as _upload;
    }
    public function __construct(){
        parent::__construct();
        $this->load->model('Ad','A');
    }
    
    /**
     * 广告列表
     * 判断过期或上线的时间段位1周=604800s
     * @param number $type         1：在线，2：即将上线，3：即将过期，4：过期
     * @param number $category_id
     * @param number $status
     * @param number $per
     * @param number $offset
     */
    public function lists($type=1,$category_id=0,$status=21,$per=10,$offset=0){
        $where = sprintf('ad_starttime < %.0f AND ad_endtime > %.0f',$t=time(),$t);
        $s = 604800;
        switch($type){
            case 2:
                $where = sprintf('ad_starttime > %.0f',$t);
            break;case 3:
                $where = sprintf('ad_endtime BETWEEN %.0f AND %.0f',$t,$t+$s);
            break;case 4:
                $where = sprintf('ad_endtime < %.0f',$t);
            break;
        }
        
        empty($category_id) || $where .= ' AND '.sprintf('category_id in (%s)',$this->C->_childrenIds($category_id));
        empty($status) || $where = sprintf('status_id = %d AND ',$status).$where;
        
        $where = $this->_search_where($where, 'ad_title like \'%%s%\'');
        
        $select = 'ad_id,ad_title,category_id,status_id,ad_starttime,ad_endtime';
        $rows = $this->_lists($this->A,['where'=>$where,'select'=>$select],
                        "esadmin/ad/lists/{$type}/{$category_id}/{$status}",$per,$offset);
        echo $this->Datagrid->init($rows,$this->A)->display().
             '<p class="load-script" data-src="/min/esadmin.ad.js"></p>';
    }
    public function id($id=0){
        $m = $this->_id($id,$this->A);
        $this->Form->init($this->A,$m)
                   ->input('ad_title')
                   ->file('ad_pic')
                   ->select('category_id',$this->_cat_select_pids($m,'category_id',TRUE))
                   ->select('status_id')
                   ->input('ad_starttime')
                   ->input('ad_endtime',['value'=>empty($m)?strtotime('+1 month'):$m->ad_endtime])
                   ->input('ad_target',$this->_defaultVal($m, 'ad_target','#'))
                   ->input('ad_sequence',$this->_defaultVal($m, 'ad_sequence','1'))
                   ->display();
    }
    
    public function upload($filename='file',$dir='ad',$thumbnail=[],$print=TRUE){
        return $this->_upload($filename,$dir,$thumbnail,$print);
    }
    
    /**
     * @param number $id
     */
    public function del($id=0){
        $r = ['err'=>1];
        die( json_encode($r) );
    }
    
    protected function dataValidate(Array $data){
        $data['ad_pic'] = $data['ad_pic-tmp'];
        $data['ad_starttime'] = strtotime($data['ad_starttime']);
        $data['ad_endtime'] = strtotime($data['ad_endtime']);
        return $data;
    }
  
}

?>