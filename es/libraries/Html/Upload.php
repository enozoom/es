<?php 
namespace es\libraries\Html;
/**
 * 上传及裁剪
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年5月10日下午2:28:14
 */
use es\core\Toolkit\File;
use es\core\Http\Request;
class Upload{
  use File,Request;
  //| 文件上传的地址
  private $upload_dir = 'uploads/';
  //| 文件产生的缩略图大小
  private $thumb = [];
  //| <form>
  //|  <input type="file" name="{$iptname}"/>
  //| </form>
  private $iptname = 'ufile';
  //| 最大尺寸 32MB=33554432,1MB=1048576,512k=533504,100K=102400 
  private $max_size = 33554432;
  //| 文件类型
  private $ext_type = ['gif','jpg','jpe','jpeg','png','mp4'];
  //| 是否包含网站host
  private $with_host = true;
  //| 上传后的图片地址
  private $pic_url = '';//源图
  private $cut_url = '';//裁切图
  private $thumb_url = '';//缩略图
  //| 上传后的图片类型
  private $mime;
  //| 上传后的图片后缀
  private $suffix;
  //| 上传后的图片名
  private $pic_name;
  
/**
 * 类入口
 * @param array $option [upload_dir,thumb,iptname,max_size,$with_host]
 * @return [url=>'源图路径',thumb=>'缩略图路径',cut=>'剪切图路径']
 */
  public function initialize($option=[]){
    $vars = ['upload_dir','thumb','iptname','max_size','$with_host'];
    foreach($option as $k=>$v){
      in_array($k, $vars) && $this->{$k} = $v;
    }
    
    $this->pic_url = $this->upload();
    $result = ['url'=>$this->pic_url,'thumb'=>''];
    
    if( !empty($this->thumb) ){
      $this->thumb();
      $result = ['thumb'=>$this->thumb_url,'cut'=>$this->cut_url]+$result;
    }
    
    if( $this->with_host ){
      foreach($result as $k=>$v){
        $result[$k] = $this->baseUrl($v);
      }
    }
    return $result;
  }
  
/**
 * 上传图片
 * @return false|上传后的地址
 */
  public function upload($iptName='',$uploadDir='',$exType=[]){
    empty($iptName) || $this->iptname = $iptName;
    empty($uploadDir) || $this->upload_dir = $uploadDir;
    empty($exType) || $this->ext_type = $exType;
    
    if( !empty($_FILES) && !empty($_FILES[$this->iptname]) && !$_FILES[$this->iptname]['error'] ){
      $size = $_FILES[$this->iptname]['size'];
      if($this->max_size > $size){
        $tname = $_FILES[$this->iptname]["tmp_name"];
        $fname = $_FILES[$this->iptname]["name"];
        
        $suffix = substr($fname, strrpos($fname, '.'));
        $this->suffix = $suffix;
        if( in_array(substr($suffix,1),$this->ext_type) ){
          $pname = sha1(time().str_replace($suffix, '', $fname));
          $this->pic_name = $pname;
          $pname = $pname.$suffix;
          
          $path = $this->mkdir( $this->upload_dir,1 ).$pname;
          if(move_uploaded_file($tname,$path)){
            return $path;
          }
        }
      }
    }
    return FALSE;
  }
  
  private function thumb(){
    $inf = getimagesize($this->pic_url);
    $this->new_wh([$inf[0],$inf[1]]);
  }
  
/**
 * 对图片进行尺寸比较，获得合适的裁剪或缩略尺寸
 * @param array $origin 实际尺寸
 * @return false|[w,h]
 */
  private function new_wh($origin=[]){
    empty($new) && $new = $this->thumb;
    
    if( ($new[0] == 0 && $new[0] == $new[1]) ||
        ($new[0]>$origin[0] || $new[1]>$origin[1])
      ){// 未设置缩略尺寸
      return false;
    }
    
    $t_w = $origin[0]*$new[1]/$origin[1];
    $t_h = $origin[1]*$new[0]/$origin[0];
    
    if(!$new[0]){// 未设置宽,或预想宽比源宽大
      return $this->to_thumb( [ceil($t_w),$new[1]] );
    }
    
    if(!$new[1]){// 未设置高,或预想高比源高大
      return $this->to_thumb( [$new[0],ceil($t_h)] );
    }
    
    if( $t_w - $new[0] <= 10 && $t_h - $new[1] <= 10 ){
      return $this->to_thumb($new);
    }
    
    // 不满足的直接缩略条件的图，先进行裁剪，然后缩略
    if( $origin[0]-$new[0] > $origin[1]-$new[1] ){
      return $this->to_cut( [ ceil($new[0]*$origin[1]/$new[1]),$origin[1] ] );
    }else{
      return $this->to_cut( [ $origin[0],ceil($origin[0]*$new[1]/$new[0]) ] );
    }
    
    
    return false;
  }
  
  private function to_thumb($size=[]){
    $pic = $this->pic_url;
    empty($this->cut_url) || $pic = $this->cut_url;
    $url = $this->generate_image($pic,$size,'thumb');
    if(!empty($url)){
      $this->thumb_url= $url;
    }    
  }
  
/**
 * 生成一个裁剪图
 * @param array $size
 */
  private function to_cut($size=[]){
    $url = $this->generate_image($this->pic_url,$size,'cut');
    if(!empty($url)){
      $this->cut_url = $url;
    }
    $this->new_wh($size);
  }
  
/**
 * 生成一个图片
 * @param string $origin_img  源图
 * @param array $size         新图的[宽，高]
 * @param string $suffix      新图的中缀(相对于$this->suffix后缀)|修改后的存放地址
 */
  private function generate_image($origin_img='',$size=[],$suffix=''){
    $_image = imagecreatetruecolor($size[0],$size[1]);
    $image = null;
    $origin_inf = getimagesize($origin_img);
    
    switch ( $origin_inf['mime'] ){
      case 'image/jpeg':case 'image/jpg':case 'image/pjpeg':
        $image = imagecreatefromjpeg($origin_img);
      break;case 'image/gif':
        $image = imagecreatefromgif($origin_img);
      break;case 'image/png':case 'image/x-png':
        $image = imagecreatefrompng($origin_img);
      break;
    }
    if(empty($image)) return false;
    
    if( $suffix=='cut' ){
      $origin_inf[0] = $size[0];
      $origin_inf[1] = $size[1];
    }
    
    if( $suffix=='thumb' ){
      $size[1] = $origin_inf[1]*$size[0]/$origin_inf[0];
    }
    
    imagecopyresampled($_image,$image,0,0,0,0,$size[0],$size[1],$origin_inf[0],$origin_inf[1]);
    
    $url = $this->mkdir($this->mkdir( $this->upload_dir,1 ).$suffix).$this->pic_name.$this->suffix;
    $flag = false;
    switch ( $origin_inf['mime'] ){
      case 'image/jpeg':case 'image/jpg':case 'image/pjpeg':
        $flag = imagejpeg($_image,$url,90);
      break;case 'image/gif':
        $flag = imagegif($_image,$url);
      break;case 'image/png':case 'image/x-png':
        $flag = imagepng($_image,$url);
      break;
    }
    imagedestroy($_image);
    return $flag?$url:$flag;
  }
  
}
