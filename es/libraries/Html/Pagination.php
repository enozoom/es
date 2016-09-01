<?php 
namespace es\libraries\Html;
/**
 * 精简版分页
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月4日下午2:24:40
 */
final class Pagination{
  public $max = 8;
  
  /**
   * 分页
   * 生成的分页为,使用$placeholder作为uri占位符，请在获取分页后使用str_replace进行替换
   *
   *   <p class="pagination">
   *       <a href="URLPLACEHOLDER/$per/$offset/">1</a>
   *       <span>2</span>
   *       <a herf="URLPLACEHOLDER/$per/$offset/">3</a>
   *   </p>
   *
   * @param int $total 总行数
   * @param int $per
   * @param int $offset
   * @param bool $data_href 使用<a data-href=""
   * @param string $placeholder URL占位符
   */
  public function init($total=0,$per=0,$offset=0,$data_href=FALSE,$placeholder='URLPLACEHOLDER'){
    if($per>$total) return '';
    
    $pages = ceil($total/$per);
    $current = $offset/$per;
    $html = '';
  
    $max = $this->max;  // 最大显示n+1个页码,因为从0开始
    $start = 0;// id一个页码
  
    if($pages>$max){// 总页码大于最大显示页码数
      if($current>=$max){// 当前页码大于最大显示页码数
        if( $pages == $current ){// 最后一页
          $start = $pages - $max;
        }else{
          $avg = floor($max/2);
          if( $current+$avg >= $pages ){// 当前码处于页码末尾部分
            $start = $pages - $max;
          }else{
            $start = $current - $avg;
          }
        }
      }
    }
  
    $limit = $start+$max;
    $limit > $pages && $limit = $pages;
    $limit == $pages && $limit-- ;
  
    $pagination = range($start, $limit);
  
    foreach($pagination as $i){
      $tag = '<span>'.($i+1).'</span>';
      if($i!=$current){
        $href = sprintf('/%s/%d/%d/',$placeholder,$per,$per*$i);
        $a = '<a href="%s">%d</a>';
        if($data_href){
          $a = '<a data-href="%s" href="#">%d</a>';
        }
        $tag = $tag = sprintf($a,$href,$i+1);
      }
      $html .= $tag;
    }
    return empty($html)?'':'<p class="pagination">'.$html.'</p>';
  }
}