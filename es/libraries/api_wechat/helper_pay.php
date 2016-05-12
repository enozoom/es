<?php namespace es\libraries\api_wechat;
/**
 * 微信支付
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年5月12日上午8:50:25
 * --------------------------
 * appid为微信公众号appid，而发送红包的openid必须为该微信公众号的的对应openid
 */
class Pay{
  protected $appid;
  protected $mchid;
  protected $key;
  protected $sslcert;
  protected $sslkey;
  protected $ip;
  protected $send_name = 'ES3.0';
  
  public function __construct($appid,$mchid, $key, $sslcert, $sslkey, $ip){
    $this->appid = $appid;
    $this->mchid = $mchid;
    $this->key = $key;
    $this->sslcert = $sslcert;
    $this->sslkey = $sslkey;
    $this->ip = $ip;
  }
  
  /**
   * 发红包
   * @param string $open_id  与$this->appid（公众号）关联生成的唯一openid
   * @param int $amount      红包的金额，单位元
   * @param array $wishings  ['wishing'=>'祝福的话','act_name'=>'活动名','remark'=>'备注']
   * @param array $share     []
   */
  public function send_hongbao($open_id,$amount,$wishings,$share=[]){
    $amount *= 100;
    $data = array('re_openid'=>$open_id, 'total_amount'=>$amount, 'total_num'=>1);
    /*
     foreach(array('total_amount','min_value','max_value','total_num') as $key){
     !key_exists($key, $amount) && show_500('$amount必须含有'.$key.'键');
     }$data += $amount;
     */
    foreach(array('wishing','act_name','remark') as $key){
      if(!key_exists($key, $wishings)){
        \es\core\log_msg( '$wishings必须含有'.$key.'键' );
        die();
      }
    }$data += $wishings;
  
    if(!empty($share)){
      extract($share);
      $data += $share;
    }
    $data['sign'] = $this->_sign($data);
    $xml = Arr2xml::toXml($data);
    
    $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
    $result = $this->curl_post_ssl($url,$xml);
    var_dump($result);
  }
  
  /**
   * 生成签名
   * 对必要参数补全。
   * @param array 生成签名的必须参数
   */
  private function _sign(&$data=array()){
    $data = $data+
            ['nonce_str'  => \es\core\random_string('alnum',32),
             'mch_billno' => $this->_mch_billno(),
             'mch_id'     => $this->mchid,
             'wxappid'    => $this->appid,
             'send_name'  => $this->send_name,
             'client_ip'  => $this->ip];
    
    ksort($data);
  
    $buff = '';
    foreach($data as $k=>$v){
      if($k != "sign" && $v != "" && !is_array($v)){
        $buff .= $k . "=" . $v . "&";
      }
    }
    return strtoupper(md5(trim($buff, "&"). "&key=".$this->key));
  }

  /**
   * 订单号
   * @param int $n 10位数字
   * @return string
   */
  private function _mch_billno($n=0){
    $n || $n = substr(time(),2).\es\core\random_string('numeric',2);
    return $this->mchid.date('Ymd').$n;
  }
  /**
   * POST提交带证书的请求
   * @param string $url 提交的地址
   * @param array $vars 提交的参数
   * @param number $second 超时时间
   * @param unknown $aHeader
   * @return mixed|boolean
   */
  private function curl_post_ssl($url, $vars, $second=30,$aHeader=array()){
    if(empty($this->sslcert) || empty($this->sslkey) || !file_exists($this->sslcert) || !file_exists($this->sslkey) ){
      show_500('cert证书文件必须设置');
    }
  
    $ch = curl_init();
    
    curl_setopt($ch,CURLOPT_TIMEOUT,$second);//超时时间
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
  

    curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
    curl_setopt($ch,CURLOPT_SSLCERT,$this->sslcert);
    curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
    curl_setopt($ch,CURLOPT_SSLKEY,$this->sslkey);
  
    if( count($aHeader) >= 1 ){
      curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
    }
  
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
    $data = curl_exec($ch);
    if($data){
      curl_close($ch);
      return $data;
    }else {
      $error = curl_errno($ch);
      \es\core\log_msg("call faild, errorCode:$error\n",'发送红包请求失败');
      curl_close($ch);
      return false;
    }
  }
}