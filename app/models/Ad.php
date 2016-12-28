<?php
namespace app\models;

use es\core\Model\ModelAbstract;

class Ad extends ModelAbstract
{
    public function _attributes($attr = '')
    {
        $attrs = [
                    'ad_id'=>'#',
                    'ad_title'=>'标题',
                    'category_id'=>'分类',
                    'ad_pic'=>'图片',
                    'ad_target'=>'目标地址',
                    'ad_starttime'=>'开始',
                    'ad_endtime'=>'结束',
                    'ad_sequence'=>'排序',
                    'status_id'=>'状态'
                 ];
        return isset($attrs[$attr])?$attrs[$attr]:$attrs;
    }
    public function __status_ids($id=0,$pid=20)
    {
        return (new Category())->__category_pids($id,$pid);
    }
    public function __category_ids($id=0)
    {
        return $this->__status_ids($id,10);
    }
}
