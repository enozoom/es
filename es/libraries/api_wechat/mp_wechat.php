<?php namespace es\libraries\api_wechat;
/**
* 微信公众号开发
* @author Joe e@enozoom.com
* 2015年10月8日 下午1:38:46
*/
class Mp_wechat extends Api_wechat{
 
/**
 * 重载callback()
 * 将微信服务器请求的数据进行解析
 * 并回调控制器的接口方法
 */
  public function callback(){
    $postXML = parent::callback();
    empty($_GET) && die();// 必须有附带参数
    extract($_GET);
    if($this->sha1_sign($postXML->Encrypt,$timest,$nonce,$msg_signature)){
      //$req = $this->crypt_extract(parent::callback()); 加密格式
      $req = parent::callback();
      //\es\core\log_msg($req);
      $reply = '';
      switch(strtolower($req->MsgType)){
        case 'text':
          $reply = \es\core\Controller::get_instance()->_keywords($req->Content);
        break;case 'event':
          $reply = \es\core\Controller::get_instance()->_events($req);
        break;
      }
      empty($reply) && die();
      list($method,$args) = $reply; 
      $args = array('to'=>$req->FromUserName,'from'=>$req->ToUserName) + $args;
      $reflector = new \ReflectionClass( '\es\libraries\api_wechat\Reply' );

      $rMethod = $reflector->getMethod( $method );
      
      $xml = $rMethod->invokeArgs( $reflector->newInstanceWithoutConstructor(), $args );
      //\es\core\log_msg($xml); // 未加密的消息体
      die($xml);
      $xml = $this->crypt_generate($xml);
      $signature = $this->set_sha1_sign($xml, $timestamp, $nonce);
      $xml = Reply::crypt_xml($xml,$signature, $timestamp, $nonce);
      //\es\core\log_msg($xml); // 加密后的消息体
      echo $xml;exit();
    }
    exit();
  }
  public function callback2(){
    $req = 
          ["ToUserName"=>"gh_49bcaf8cc4e3",
           "FromUserName"=>"oW_TPvgpseeggbc3_glfH9tkjPNc",
           "CreateTime"=>"1461149688",
           "MsgType"=>"event",
           "Event"=>"SCAN",
           "EventKey"=>"123",];
          
    $req = json_decode( json_encode($req) );
          
          $reply = '';
          switch(strtolower($req->MsgType)){
            case 'text':
              $reply = \es\core\Controller::get_instance()->_keywords($req->Content);
              break;case 'event':
                $reply = \es\core\Controller::get_instance()->_events($req);
                break;
          }
          empty($reply) && die();
          list($method,$args) = $reply;
          $args = array('to'=>$req->FromUserName,'from'=>$req->ToUserName) + $args;
          $reflector = new \ReflectionClass( '\es\libraries\api_wechat\Reply' );
          
          $rMethod = $reflector->getMethod( $method );
          
          $xml = $rMethod->invokeArgs( $reflector->newInstanceWithoutConstructor(), $args );
          //\es\core\log_msg($xml); // 未加密的消息体
          die($xml);
          $xml = $this->crypt_generate($xml);
          $signature = $this->set_sha1_sign($xml, $timestamp, $nonce);
          $xml = Reply::crypt_xml($xml,$signature, $timestamp, $nonce);
          //\es\core\log_msg($xml); // 加密后的消息体
          echo $xml;exit();
  }
}