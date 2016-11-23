<?php
namespace es\libraries\Wechat;
/**
 * 微信公众号开发
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年10月23日下午2:15:36
 */
class MpWechat extends WechatAbstract
{
    /**
     * 重载callback()
     * 将微信服务器请求的数据进行解析
     * 并回调控制器的接口方法
     */
    public function callback(){
        $req = parent::callback();
        empty($_GET) && die();// 必须有附带参数
        extract($_GET);
        if($this->sha1_sign($req->Encrypt,$timestamp,$nonce,$msg_signature)){
            $reply = '';
            $ES = \es\core\Controller\ControllerAbstract::getInstance();
            switch(strtolower($req->MsgType)){
                case 'text':
                    $reply = $ES->_keywords($req->Content);
                break;case 'event':
                    $reply = $ES->_events($req);
                break;
            }
            
            empty($reply) && die();
            list($method,$args) = $reply;
            $args = array('to'=>$req->FromUserName,'from'=>$req->ToUserName) + $args;
            $reflector = new \ReflectionClass( '\es\libraries\Wechat\ReplyStatic' );
            $rMethod = $reflector->getMethod( $method );

            $xml = $rMethod->invokeArgs( $reflector->newInstanceWithoutConstructor(), $args );
            //$this->log($xml); // 未加密的消息体
            //die($xml);
            $xml = $this->crypt_generate($xml);
            $signature = $this->set_sha1_sign($xml, $timestamp, $nonce);
            $xml = ReplyStatic::crypt_xml($xml,$signature, $timestamp, $nonce);
            //$this->log($xml); // 加密后的消息体
            echo $xml;exit();
        }
        exit();
    }

}