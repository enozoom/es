<?php
namespace es\libraries\Wechat;

use es\core\Log\LogTrait;
use es\core\Toolkit\AryTrait;
use es\core\Toolkit\FileStatic;
use es\core\Http\RequestTrait;
use es\libraries\Wechat\CryptStatic;


/**
 * 微信企业号
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年12月8日14:12:46
 */
class QyWechat
{
    use LogTrait,AryTrait,RequestTrait;
    // 应用中心->相应程序->回调模式中获取
    private $EncodingAESKey;
    // 设置->企业号信息->帐号信息查找
    private $CorpID;
    // 设置->权限设置->普通管理组:新建->获取
    private $Secret;
    private $Token = 'enozoomstudio';
    public function __construct($e,$c,$s,$t='')
    {
        $this->EncodingAESKey = $e;
        $this->CorpID = $c;
        $this->Secret = $s;
        empty($t) || $this->Token = $t;
    }
    
    public function isWechat()
    {
        if( $this->isRequired(['msg_signature','timestamp','nonce'],$_GET) ){
            extract($_GET);
            if(empty($_GET['echostr'])){
                
            }else{// 第一次绑定
                $echostr = urldecode($echostr);
                $ary = [$this->Token,$timestamp,$nonce,$echostr];
                sort($ary,SORT_STRING);
                if( $msg_signature == sha1( implode($ary) ) ){
                    die( CryptStatic::decrypt($echostr, $this->CorpID, $this->EncodingAESKey) );
                }
            }
            
        }
    
    }
    
    protected function accessToken(){
        $token_file = FileStatic::mkDir(APPPATH.'cache/wechat').$this->CorpID.'.json';
        if( file_exists($token_file) && 
            !empty($json = json_decode( file_get_contents($token_file) )) &&
            $json->expires > time() ){
            return $json->access_token;
        }
        
        $url = sprintf('https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s',$this->CorpID,$this->Secret);
        $json = json_decode( $this->curlGet($url) );
        if(!empty($json->errcode)){ // 获取失败直接停止
            die('get access_token fail!' );
        }
        $data = ['expires'=>time()+$json->expires_in, 'access_token'=>$json->access_token];
        file_put_contents($token_file, json_encode($data) );
        return $json->access_token;
    }
    
    
    public function reply(){
        
    }
    
    /**
     * 发送消息
     * http://qydev.weixin.qq.com/wiki/index.php?title=消息类型及数据格式
     * 
     * @param array $content  消息内容
     * @param number $agentid 应用的id,在应用的设置页面查看
     * @param string $touser  发送对象，多个用|分隔，@all则发给所有人
     * @param string $msgtype 消息类型 text|image|voice|video..
     * @param number $safe    表示是否是保密消息，0表示否，1表示是，默认0
     * @param string $toparty 部门ID列表，多个接收者用|分隔,当$touser为@all该参数无效
     * @param string $totag   标签ID列表，多个接收者用|分隔,当$touser为@all该参数无效
     * @return bool
     */
    public function send($content=['content'=>''],$agentid=0,$touser='@all',$msgtype='text',$safe=0,$toparty='',$totag=''){
        $url = sprintf('https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=%s',$this->accessToken());
        $data = ['msgtype'=>$msgtype,'agentid'=>$agentid,$msgtype=>$content];
        foreach( ['touser','toparty','totag','safe'] as $arg) empty($$arg) || $data[$arg] = $$arg;
        $j = $this->curlPost($url, json_encode($data,JSON_UNESCAPED_UNICODE));
        return $j->errcode==0;
    }
    
}