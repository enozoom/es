<?php 
namespace es\libraries\api_wechat;

require 'helper_arr2xml.php';   // 数组转xml
require 'helper_reply.php';     // 微信回复消息
require 'helper_crypt.php';     // 微信消息加解密
require 'helper_material.php';  // 微信消息加解密
require 'helper_wjs.php';       // 网页js交互获取分享，用户等信息

/**
 * 微信公众号基本类
 */
class Api_wechat{
  protected $appid;
  protected $appSecret;
  protected $token = 'enozoomstudio';
  protected $encodingAesKey;
  protected $access_token;

// --------------------------------------------------------------------------------------
// 与微信服务器数据交互
// --------------------------------------------------------------------------------------

/**
 * 响应并验证微信的请求
 * @return boolean
 */  
  public function _is_wechat(){
    empty($_GET['timest']) || $_GET['timestamp'] = $_GET['timest'];
    if(empty($_GET['signature']) || empty($_GET['timestamp']) || empty($_GET["nonce"])){
      exit();
    }
    
    $signature = $_GET['signature'];
    $timestamp = $_GET['timestamp'];
    $nonce = $_GET["nonce"];
    $tmpArr = array($this->token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );
    $flag = $tmpStr == $signature;
    if(empty($_GET['echostr'])){
      return $flag;
    }
    $flag && die($_GET['echostr']);
  }
  
/**
 * 对核心变量赋值
 * @param string $appid
 * @param string $appSecret
 * @param string $token
 * 
 * @return
 */
  public function _init($appid,$appSecret,$encodingAesKey,$token=''){
    if( empty($appid) || empty($appSecret) || empty($encodingAesKey) ){
      show_500('必须保证$appid,$appSecret,$encodingAesKey有值且正确');
    }
    
    $this->appid = $appid;
    $this->appSecret = $appSecret;
    $this->encodingAesKey = $encodingAesKey;
    empty($token) || $this->token = $token;
    $this->access_token = $this->access_token();
    return $this;
  }
  
/**
 * 获取access_token 字符串
 * @return string
 */  
  public function access_token(){
    $token_file = \es\core\mk_dir(APPPATH.'cache/wechat').$this->appid.'.json';
    $get = FALSE;// 是否需要再次远程获取
    if(!file_exists($token_file)){
      $get = TRUE;
    }else{
      $json = json_decode( file_get_contents($token_file) );
      if($json->expires < time()){
        $get = TRUE;
      }else{
        return $json->access_token;
      }
    }
    if($get){
      $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
      $json = json_decode(\es\core\curl_file_get_contents(sprintf($url,$this->appid,$this->appSecret)));
      var_dump($this->appid,$this->appSecret);
      if(!empty($json->errcode)){ // 获取失败直接停止
        \es\core\log_msg($json,'获取公共access_token失败');
        die('get access_token fail!' );
      }
      $data = array('expires'=>time()+$json->expires_in,
                    'access_token'=>$json->access_token);
      file_put_contents($token_file, json_encode($data) );
      return $json->access_token;
    }
    return '';
  }

/**
 * 将POST提交来的XML数据变成对象
 * @return SimpleXMLElement
 */
  public function callback(){
    $postStr = @file_get_contents("php://input");
//    log_msg($postStr,'poststr');
    empty($postStr) && die();
    $postObj = simplexml_load_string($postStr,'SimpleXMLElement', LIBXML_NOCDATA);
    return $postObj;
  }

/**
 * 生成签名
 * @param string $noncestr     随机字符串
 * @param string $jsapi_ticket JS票据
 * @param int $timestamp       时间戳
 * @param string $url          当前网址
 * 
 * @return
 */
  public function jsapi_sign($noncestr,$timestamp,$url=''){
    return WJS::jsapi_sign($this->access_token, $noncestr, $timestamp, $url);
  }

//--------------------------------------------------------------------------------------
// 微信的菜单方法
//--------------------------------------------------------------------------------------

/**
 * 生成一个微信获权跳转回来的地址
 * @param  string $redirect 跳转地
 * @param  string $scope 范围：snsapi_base 静默模式，snsapi_userinfo 需手动确定
 * @return string
 */
  public function wechat_openid_link($redirect,$scope='snsapi_base'){
    $https = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=ES#wechat_redirect';
    $url = str_replace('http%3A%2F%2F','http://', urlencode($redirect));
    $url = urlencode($redirect);
    return sprintf($https,$this->appid,$url,$scope);
  }
  
  /**
   * 根据微信跳转而来的带有code参数的链接
   * 获取当前微信用户对应当前公众号的唯一openid
   * @param string $code
   * @return string
   */
  public function user_openid($code){
    return WJS::user_openid($code, $this->appid, $this->appSecret);
  }
  
  
  
/**
 * 生成底部菜单
 * @param array $data
 * $data =
 * array(
 *   array('name'=>'菜单1','type'=>'view','url'=>'http://www.enozoom.com'),
 *   array('name'=>'菜单2','type'=>'click','key'=>'KEY'),
 *   array('name'=>'菜单3','sub_button'=>array(
 *       array('name'=>'菜单3-1','type'=>'view','url'=>'http://www.enozoom.com'),
 *       ...
 *     )
 *   )
 * )
 * 更多关于菜单http://mp.weixin.qq.com/wiki/13/43de8269be54a0a6f64413e4dfa94f39.html
 * 
 * @return bool 是否成功
 */
  public function generate_menu($data){
    $menus = json_encode( array('button'=>$data), JSON_UNESCAPED_UNICODE );
    //$menus = array('button'=>$data);
    $url = sprintf('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s',$this->access_token);
    $json = \es\core\curl_post_data($url, $menus,1);
    return $json->errcode == 0;
  }
  
//--------------------------------------------------------------------------------------
// 微信的常用方法
//--------------------------------------------------------------------------------------

/**
 * 微信分享时的必要数据
 */
  public function share_data(){
   $t = time();
   $s = \es\core\random_string('alnum',16);
   return array('appId' => $this->appid,
            'timestamp' => $t,
     'nonceStr' => $s,
     'signature' => $this->jsapi_sign($s, $t));
  }
  
/**
 * 对加密微信消息进行提取
 * @param string $msg_crypt 待解密消息
 */
  public function crypt_extract($msg_crypt){
    is_object($msg_crypt) || die();
    return Crypt::decrypt($msg_crypt->Encrypt.'',$this->appid,$this->encodingAesKey);
  }
  
/**
 * 对正常消息进行加密
 * @param string $xml
 * @return SimpleXMLElement
 */
  public function crypt_generate($xml){
    return Crypt::encrypt($xml,$this->appid,$this->encodingAesKey);
  }
  
/**
 * 加密体签名
 * 
 * @param string $encrypt_msg 加密体
 * @param int $timest 消息时间戳
 * @param string $nonce 消息的随机数
 * @param string $msgSignature 消息的签名 
 * @return boolean|string 存在$msgSignature值则返回bool，否则返回签名
 */
  public function sha1_sign($encrypt, $timestamp, $nonce,$msgSignature=''){
     $array = array($encrypt, $this->token, $timestamp, $nonce);
     sort($array, SORT_STRING);
     $str = implode($array);
     return empty($msgSignature)?sha1($str):sha1($str) == $msgSignature;
  }
  
  public function set_sha1_sign($encrypt,&$timestamp, &$nonce){
    $timestamp = time();
    $nonce = random_string('numeric');
    return $this->sha1_sign($encrypt, $timestamp, $nonce);
  }
}