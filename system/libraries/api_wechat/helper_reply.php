<?php defined('APPPATH') OR exit('POWERED BY Enozoomstudio');
/**
* 对反馈消息进行封装
* @author Joe e@enozoom.com
* 2015年10月9日 上午11:25:33
*/
class Reply{
  
/**
 * 回复一段文字
 * @param string $to
 * @param string $from
 * @param string $content 回复内容
 * @param number $agentid 企业号的应用ID
 * @return string XML
 */
  public static function text($to,$from,$content,$agentid=0){
    $data = array('ToUserName'=>$to,    'FromUserName'=>$from,
                  'CreateTime'=>time(),      'MsgType'=>'text',
                     'Content'=>$content);
    empty($agentid) || $data['AgentID'] = $agentid;
    return Arr2xml::toXml($data);
  }
  
/**
 * 回复图文信息
 * @param string $to
 * @param string $from
 * @param array $articles 文章组 array(array(Title=>,Description=>,PicUrl=>,Url=>),..)
 * @return string XML
 */
  public static function article($to,$from,$articles){
    $count = count($articles);
    
    if(isset($articles['Title'])){// 只有一篇图文
      $articles = array('item'=>$articles);
      $count = 1;
    }else{// 多个图文
      $_articles = $articles;$articles = array();
      for($i = 0; $i < $count; $i++){
        $articles['item'.$i] = $_articles[$i];
      }
    }
    
    $data = array('ToUserName'=>$to,   'FromUserName'=>$from,
                  'CreateTime'=>time(),     'MsgType'=>'news',
                'ArticleCount'=>$count,    'Articles'=>$articles );
    
    $xml = Arr2xml::toXml($data);// 需要对item0之类的额外加的数字标签去数字化，item0=>item
    $xml = preg_replace('>item(\d*)>', 'item', $xml);
    return $xml;
  }
  
  public static function crypt_xml($encrypt, $signature, $timestamp, $nonce){
    $data = array('Encrypt'=>$encrypt,'MsgSignature'=>$signature,'TimeStamp'=>$timestamp,'Nonce'=>$nonce);
    return Arr2xml::toXml($data);
  }
}