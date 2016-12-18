<?php
namespace app\models;

use es\core\Model\ModelAbstract;

class Article extends ModelAbstract
{
    public function _attributes($attr = '')
    {
        $attrs = [
                    'article_id'=>'#',
                    'article_title'=>'标题',
                    'article_keywords'=>'关键词',
                    'article_description'=>'描述',
                    'article_pic'=>'封面',
                    'article_txt'=>'内容',
                    'category_id'=>'分类',
                    'status_id'=>'状态',
                    'article_author'=>'作者',
                    'article_timestamp'=>'时间',
                 ];
        return isset($attrs[$attr])?$attrs[$attr]:$attrs;
    }
    
    public function _insert(Array $array){
        empty($array['article_timestamp']) && $array['article_timestamp'] = time();
        return parent::_insert($array);
    }
}
