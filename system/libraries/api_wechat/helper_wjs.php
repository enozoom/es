<?php defined('APPPATH') OR exit('POWERED BY Enozoomstudio');
/**
* 网页获取用户信息所需的方法们
* @author Joe e@enozoom.com
* 2015年10月31日 下午1:36:41
*/

class WJS{
  
/**
 * 缓存文件的存放路径
 * @return string
 */
  public static function cache_file(){
    return mk_dir(APPPATH.'cache/wechat').'ticket.json';
  }
  
 /**
  * 获取JS票据
  * @param string $access_token 
  * @return string
  */
  public static function jsapi_ticket($access_token){
    // 从没有获取过access_token或者已经过期
    $path = self::cache_file();
   
    if(file_exists($path)){
      $ticket = json_decode( file_get_contents($path) );
      if($ticket->expires>time()){
       return $ticket->ticket;
      }
    }
   
    $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi';
    $json = json_decode(curl_file_get_contents(sprintf($url,$access_token)));
    if( $json->errcode ){
      log_msg($json,'获取jsapi_ticket失败');
      die('get jsapi_ticket fail!' );
    }else{
      $ticket = array('expires'=>time()+$json->expires_in,'ticket'=>$json->ticket);
      file_put_contents($path, json_encode($ticket) );
      return $json->ticket;
    }
   
    return '';
 }
 /**
  * 生成签名
  * @param string $noncestr     随机字符串
  * @param string $jsapi_ticket JS票据
  * @param int    $timestamp    时间戳
  * @param string $url          当前网址
  *
  * @return
  */
  public static function jsapi_sign($access_token,$noncestr,$timestamp,$url=''){
    $jsapi_ticket = self::jsapi_ticket($access_token);
    empty($url) && $url = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    
    $args = array( 'noncestr'=>$noncestr,'jsapi_ticket'=>$jsapi_ticket,
                  'timestamp'=>$timestamp,'url'=>$url );
    ksort($args);
    $query = str_replace( array('%2F', '%3A', '%3F', '%3D', '%26'),
                          array('/'  ,   ':',   '?',   '=', '&'),
                          http_build_query($args) );
    return sha1($query);
  }
  
/**
 * 获取当前微信用户的openid,
 * 前提必须是api_wechat->wechat_openid_link()或其他生成一个微信获权跳转回来的地址
 * 地址带有?code=CODE参数，依靠参数获取openid
 * 本方法仅到获取openid未知，不再获取用户详细信息。
 * 
 * @param  string $code
 * @param  string $appid
 * @param  string $appsecret
 * @return string $openid 如果正常则获取openid或返回空字符串
 */
  public static function user_openid($code,$appid,$appsecret){
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
    $url = sprintf($url,$appid,$appsecret,$code);
    $json = json_decode( curl_file_get_contents($url) );
    if( !empty($json->errcode) ){
      log_msg("errcode=$json->errcode\ncode={$code}",'openid获取失败');
      return '';
    }
    return $json->openid;
  }
  
}