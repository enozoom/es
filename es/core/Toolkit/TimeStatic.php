<?php
namespace es\core\Toolkit;

class TimeStatic{
  
  /**
   * 格式一个时间戳
   * @param number $time
   * @param string $format
   */
  public static function formatTime($time=0,$format='Y-m-d H:i:s')
  {
      return date($format,(empty($time) || !is_numeric($time) )?time():$time);
  }
  
  /**
   * 获取一个时间段的名称
   * @param number $h  24制小时数
   * @return string
   */
  public static function timeSegment($h=0)
  {
    empty($h) && $h = date('H');
    $segs =  ['午夜',1=>'凌晨',4=>'黎明',7=>'清晨',10=>'上午',13=>'中午',16=>'下午',19=>'傍晚',22=>'深夜'];
    $key = function($k) use ($segs,&$key){
      return key_exists($k, $segs)?$segs[$k]: $key(--$k);
    };
    return $key( (int)$h );
  }
  
  /**
   * t到t2的截至时间，年/月/天/时/分/秒
   * @param number $t
   * @param number $t2
   * return [Y=>,m=>,d=>,H=>,i=>,s=>]
   */
  public static function timeDown($t,$t2=0){
      empty($t2) && $t2 = time();
      $d = new \DateTime();
      $d2 = new \DateTime();
      $d->setTimestamp($t); $d2->setTimestamp($t2);
      $date = date_diff($d, $d2);
      return ['Y'=>$date->y,'m'=>$date->m,'d'=>$date->d,'H'=>$date->h,'i'=>$date->i,'s'=>$date->s];
  }
  
  /**
   * 根据时间规律获得一个始终在正增长的数字
   * @param number $multiplier 乘积的乘数
   * @param number $t
   */
  public static function n($multiplier=1,$t=0){
      $t>1&&$t=1;
      $ns = [date('W')+date('m'),date('z')+date('W')+date('n')];
      return ceil($ns[$t]*$multiplier);
  }
  
  /**
   * 获取某个月的天数
   * @param unknown $m
   */
  public static function m($m,$Y=0){
      $Y && $Y = date('Y');
      return date( 't',strtotime($Y.'-'.$m.'-1') );
  }
  

}