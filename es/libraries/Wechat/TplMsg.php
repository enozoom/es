<?php
namespace es\libraries\Wechat;
use es\core\Http\RequestTrait;
use es\core\Log\Logger;
/**
 * 模板消息
 * @author Joe
 * 2016年12月4日15:00:18
 */
class TplMsg
{
    use RequestTrait;
    
    /**
     * 发送一条模板消息
     * @param string $template_id   模板ID
     * @param string $openid        关注微信公众号的用户openid
     * @param array $data           模板的数据
     *             $data = [
                                'first'=>['value'=>'','color'=>''],
                                'keyword1'=>['value'=>'','color'=>''],
                                'keyword2'=>['value'=>'','color'=>''],
                                'keyword3'=>['value'=>'','color'=>''],
                                'remark'=>['value'=>'','color'=>''],
                            ]
     * 
     * @param string $url           点开模板消息，可为空
     * @param string $access_token  
     * @return bool 是否成功
     */
    public function send($template_id,$openid,$data,$url,$access_token){
        $data = [
            'touser'=>$openid,
            'template_id'=>$template_id,
            'data'=>$data
        ];
        
        empty($url) || $data['url'] = $url;
        $json = $this->curlPost("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}", 
                                json_encode($data,JSON_UNESCAPED_UNICODE) );
        //$this->log($json);
        return !$json->errcode;
    }
    
    private function log($mix){
        Logger::getInstance()->debug($mix);
    }
}
