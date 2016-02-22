<?php
namespace es\libraries\eshtml;
/**
 * 分页的接口专用
 * 2015年8月18日14:06:36
 * --------------------
 * 2016年2月22日11:27:36
 * 修正$per与$total正好成整倍数关系时的页码错误
 */
class Pagination{
  private $total;     // 共多少页

  private $model;     // 输出
  private $per = 10;  // 每页多少条
  private $page = 0;  // 当前页
  private $url;       // 当前URL中含有页面参数
  
/**
* 初始化
* @param int    $total 总共多少条数据
* @param string $q     URL中的页面参数
* @param int    $per   每页多少条
* @param int    $page  当前页
* @return
*/  
  public function init($model,$url,$where='',$per=10,$offset=0){
    $this->model = $model;
    $this->url = $url;
    $this->per = $per;
    $this->page = $per?$offset/$per:0;    
    $rows = $this->model->_get_totalnum($where);
    $this->total =  ceil($rows/$per)-1;// 默认是从0开始，所以减去一页
    return $this;
  }
/**
* 计算应该显示的页码 
* @return array
*/
  private function pages(){
    $pagenum = array();
    if($this->page == 0){// 当前第一页
      if($this->total <= 2){
        for($i=0;$i<=$this->total;$i++){
          $pagenum[] = $i;
        }
      }
      
    }elseif($this->total == $this->page){// 最后一页
      if($this->total>=2){
        for($i=2;$i>=0;$i--){
          $pagenum[] = $this->total-$i;
        }
      }
    }else{// 中间页
      if($this->total>4){// 总页面数大于5页，默认从0开始
        if($this->page == 1){// 如果当前页处于第一页（0）附近
          for($i=0;$i<3;$i++){
            $pagenum[] = $i;
          }
          $pagenum[] = $this->total;
        }elseif($this->page == $this->total-1){// 当前页处于最后一页附近
          $pagenum[] = 0;
          for($i=$this->total-3;$i<$this->total;$i++){
            $pagenum[] = $i;
          }
        }else{// 当前页在页码中间
          $pagenum[] = 0;
          for($i=$this->page-1;$i<$this->page+2;$i++){
            if($i<$this->total) $pagenum[] = $i;
          }
          if($pagenum[count($pagenum)-1]+1<=$this->total){
            $pagenum[] = $this->total;
          }
        }
      }
    }
    if(empty($pagenum)){
      
      $n = $this->total>4?4:$this->total; 
      for($i=0;$i<=$n;$i++){
        $pagenum[] = $i;
      }
    }
    return $pagenum;
  }
    
  private function _pages(){
    $as = '';
    foreach($this->pages() as $p){
      $_p = $p+1;
      if($p == $this->page){
        $as .= "<strong>{$_p}</strong>";
      }else{
        $href = sprintf('%s/%s/%s', $this->url, $this->per, $this->per*$p) ;
        //$href = preg_replace('@([^:]+)\/+[^\w]+@', '$1/$2', $href);
        $as .= sprintf('<a href="%s">%d</a>',$href,$_p);
      }
      
    }
    return $as;
  }
  
  public function display(){
    $html = '<div class="eno_pagination">'.$this->_pages().'</div>';
    return  $this->total>0?$html:'';
  }
}