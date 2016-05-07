<?php namespace es\libraries\api_wechat;
/**
 * 二维码的生成
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年4月20日下午5:25:25
 * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443433542&token=&lang=zh_CN
 */
class Qr{

/**
 * 获取二维码
 * @param string $access_token
 * @param mix $scene int|string 场景ID，如果传入string则生成scene_str，int则生成scene_id
 * @return string 图片地址
 */
  public static function ticket($access_token='',$scene=''){
    $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
    
    $data = ['action_name'=>'QR_LIMIT_STR_SCENE',
             'action_info'=>['scene'=>['scene_str'=>$scene]]
            ];
    $data = json_encode($data);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    
    $return = curl_exec($ch);
    curl_errno($ch) && $return = '';// 出现异常
    
    $json = @json_decode($return);
    if( !empty($json) && empty($json->errcode) ){
      $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s';
      $url = sprintf($url,urlencode(sprintf($json->ticket)));
      return $url;
    }
    return '';
  }
}
//{\"action_name\":\"QR_LIMIT_SCENE\",\"action_info\":{\"scene\":{\"scene_id\":123}}}
//{\"action_name\":\"QR_LIMIT_SCENE\",\"action_info\":{\"scene\":{\"scene_id\":12}}}