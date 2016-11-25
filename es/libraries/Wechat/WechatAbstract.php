<?php
namespace es\libraries\Wechat;
/**
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年10月23日上午11:11:35
 */

use es\core\Log\LogTrait;
use es\core\Http\RequestTrait;
use es\core\Toolkit\AryTrait;
use es\core\Toolkit\StrStatic;
use es\core\Toolkit\FileStatic;
use es\libraries\wechat\QrStatic;

abstract class WechatAbstract
{
    use AryTrait,RequestTrait,LogTrait;
    protected $appid;
    protected $appSecret;
    protected $token = 'enozoomstudio';
    protected $encodingAesKey;
    protected $access_token;
    
    public function __construct($appid,$appSecret,$encodingAesKey,$token=''){
        if( empty($appid) || empty($appSecret) || empty($encodingAesKey) ){
            $this->show_503('必须保证$appid,$appSecret,$encodingAesKey有值且正确');
        }
        $this->appid = $appid;
        $this->appSecret = $appSecret;
        $this->encodingAesKey = $encodingAesKey;
        empty($token) || $this->token = $token;
        $this->access_token = $this->access_token();
        $this->WJS = new WJS();
    }
    
    // --------------------------------------------------------------------------------------
    // 与微信服务器数据交互
    // --------------------------------------------------------------------------------------
    
    /**
     * 响应并验证微信的请求
     * @return boolean
     */
    public function _is_wechat()
    {
        if( $this->isRequired(['signature','timestamp','nonce'],$_GET) )
        {
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
    }
    
    /**
     * 获取access_token 字符串
     * @return string
     */
    public function access_token(){
        $token_file = FileStatic::mkDir(APPPATH.'cache/wechat').$this->appid.'.json';
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
            $json = json_decode($this->curlGet(sprintf($url,$this->appid,$this->appSecret)));
            if(!empty($json->errcode)){ // 获取失败直接停止
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
    protected function jsapi_sign($noncestr,$timestamp,$url=''){
        return $this->WJS->jsapi_sign($this->access_token, $noncestr, $timestamp, $url);
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
    protected function user_openid($code){
        return $this->WJS->user_openid($code, $this->appid, $this->appSecret);
    }
    
    /**
     * 根据微信跳转而来的带有code参数的链接
     * 获取openid，然后使用openid获取当前用户的信息
     * @param string $code
     * @return object
     */
    protected function user_info($code){
        $openid = $this->user_openid($code);
        return $this->WJS->usr_info_by_openid($openid,$this->access_token);
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
        $menus = json_encode( ['button'=>$data], JSON_UNESCAPED_UNICODE );
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s',$this->access_token);
        $json = $this->curlPost($url, $menus,1);
        return $json->errcode == 0;
    }
    
    //--------------------------------------------------------------------------------------
    // 微信的常用方法
    //--------------------------------------------------------------------------------------
    
    /**
     * 生成二维码
     * @param mix $scene int|string 场景ID，如果传入string则生成scene_str，int则生成scene_id
     */
    protected function qr($scene){
        return QrStatic::ticket($this->access_token,$scene);
    }
    
    /**
     * 微信分享时的必要数据
     */
    protected function share_data(){
        $t = time();
        $s = StrStatic::randomString('alnum',16);
        return [
                  'appId' => $this->appid,
                  'timestamp' => $t,
                  'nonceStr' => $s,
                  'signature' => $this->jsapi_sign($s, $t)
               ]
        ;
    }
    
    /**
     * 对加密微信消息进行提取
     * @param string $msg_crypt 待解密消息
     */
    protected function crypt_extract($msg_crypt){
        is_object($msg_crypt) || die();
        return CryptStatic::decrypt($msg_crypt->Encrypt.'',$this->appid,$this->encodingAesKey);
    }
    
    /**
     * 对正常消息进行加密
     * @param string $xml
     * @return SimpleXMLElement
     */
    protected function crypt_generate($xml){
        return CryptStatic::encrypt($xml,$this->appid,$this->encodingAesKey);
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
    protected function sha1_sign($encrypt, $timestamp, $nonce,$msgSignature=''){
        $array = [$encrypt, $this->token, $timestamp, $nonce];
        sort($array, SORT_STRING);
        $str = implode($array);
        return empty($msgSignature)?sha1($str):sha1($str) == $msgSignature;
    }
    
    protected function set_sha1_sign($encrypt,&$timestamp, &$nonce){
        $timestamp = time();
        $nonce = StrStatic::randomString('numeric');
        return $this->sha1_sign($encrypt, $timestamp, $nonce);
    }
}