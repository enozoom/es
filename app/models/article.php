<?php namespace app\models;
/**
 * 数据模型
 * @author ES3.0160219 
 * 2016-02-24 09:14:58
 */
class Article extends Model{
  protected $tableName = 'article';
  protected $primaryKey = 'article_id';  
  public function _attributes($attr=''){
    $atts = [
              'article_id' => '#',
              'article_title' => '标题',
              'article_keywords' => '关键词',
              'article_description' => '描述',
              'pic_id' => '封面',
              'article_txt' => '内容',
              'category_id' => '分类',
              'status_id' => '状态',
              'article_author' => '作者',
              'article_timestamp' => '时间',
            ];
    return empty($attr)?$atts:(isset($atts[$attr])?$atts[$attr]:FALSE);
  }
  
  public function __category_ids($id=0){ return $this->__categories($id,3); }
}