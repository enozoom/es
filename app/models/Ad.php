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
                    'ad_target'=>'目标地址'
                 ];
        return isset($attrs[$attr])?$attrs[$attr]:$attrs;
    }
}
