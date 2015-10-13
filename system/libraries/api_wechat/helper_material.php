<?php defined('APPPATH') OR exit('POWERED BY Enozoomstudio');
/**
* 微信素材操作
* @author Joe e@enozoom.com
* 2015年10月12日 上午10:53:06
*/
class Material{
  // 默认图文的作者
  public static $author = 'ES Joe';
  
/**
 * 提交到微信服务器的地址
 * @param string $url_segment 服务器地址的其他部分
 * @param string $access_token 
 */
  public static function post_url($url_segment,$access_token){
    $url = 'https://api.weixin.qq.com/cgi-bin/%s?access_token=%s';
    return sprintf($url,$url_segment,$access_token);
  }
/**
 * 上传永久图片素材
 * @param string $img
 * @return string URL
 */
  public static function upload_img($img='',$access_token=''){
    $url = self::post_url('media/uploadimg', $access_token);
    //$img_path = str_replace('/', '\\', getcwd()."/uploads/build/whoami.png") ;
    
    if(strpos($img, 'http')!==FALSE){
      $file = curl_file_get_contents($img);
      $type = substr($img,strrpos($img, '.')+1);
      $img = '/uploads/build/tmp_wechat.'.$type;
      file_put_contents('.'.$img, $file);
    }
    
    $img = str_replace('/', '\\', getcwd().$img) ;
    
    $ch = curl_init();
    // 微信服务器CURL版本，低于PHP5.5
    //$cfile = curl_file_create($img_path,'image/png','media');
    $_data = array('media'=>'@'.$img);
    
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST,true);
    
    @curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE);
    @curl_setopt($ch, CURLOPT_POSTFIELDS,$_data);
    
    curl_setopt($ch, CURLOPT_HEADER, false);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE );
    curl_setopt($ch, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    
    $return_data = curl_exec($ch);
    curl_close($ch);
    
    if(is_string($return_data)){
      $json = json_decode($return_data);
      $return_data = empty($json->errcode)?$json->url:false;
    }
    return $return_data;
  }
  
/**
 * 获取一个图片素材
 * @param int $media_id
 * @return string URL
 */
  public static function get_img($media_id){
     $this->get_material($media_id,$access_token);
  }
  
/**
 * 上传永久图文素材
 * @param array $articles array(array(title=>,thumb_media_id=>,author=>,digest=>,show_cover_pic=>,content=>,content_source_url=>),..)
 * 参数说明参考:http://mp.weixin.qq.com/wiki/14/7e6c03263063f4813141c3e17dd4350a.html
 * 
 */
  public static function upload_news($articles){
    
  }
  
/**
 * 获取图文素材
 * @param int $media_id
 */
  public static function get_news($media_id){
   
  }
  
/**
 * 获取素材
 * @param int $media_id
 */
  public static function get_material($media_id,$access_token){
    $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=%s';
    return curl_post_data(sprintf($url,$access_token), array("media_id"=>$media_id));
  }
  
/**
 * 获取素材列表
 * @param string $access_token
 * @param string $type image|video|voice|news
 * @param int $per 返回素材的数量，取值在1到20之间 
 * @param int $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回 
 */
  public static function batch($access_token,$type='image',$per=10,$offset=0){
    $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=%s';
    
    $data = json_encode( array('type'=>$type,'offset'=>$offset,'count'=>$per) );
    
    $json = curl_post_data(sprintf($url,$access_token),$data);
    return $json;
  }
  
}