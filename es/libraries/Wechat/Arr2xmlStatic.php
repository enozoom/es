<?php
namespace es\libraries\Wechat;
/**
 * 将数组转换成XML
 * 2016年11月23日13:49:31
 */
class SimpleXMLExtended extends \SimpleXMLElement{
  public function addCData($cdata_text){
    if(!is_numeric($cdata_text)){
      $node = dom_import_simplexml($this);
      $no   = $node->ownerDocument;
      $node->appendChild($no->createCDATASection($cdata_text));      
    }
    
  }
}

//header('Content-Type: text/xml');
/**
* 数组转换成XML
* 深度节点二层
*/
class Arr2xmlStatic{
/**
* 增加节点值
* @param array $data
* @param SimpleXMLElement $xml
* 
* @return void
*/  
  public static function node($data,SimpleXMLExtended &$xml){
    foreach($data as $k=>$v){
      if(is_array($v)){
        is_numeric($k) && $k = substr($xml->getName(),0,-1);
        $subroot = $xml->addChild($k);
        self::node($v,$subroot);
      }else{
        if(is_numeric($v)){
          $xml->addChild($k,$v);
        }else{
          $xml->addChild($k)->addCData($v);
        }
      }
    }
  }
  
/**
* 直接将数组转化为xml
* @param array $data  需要被转的数组
* @param string $root 数组的根标签
* @param bool $return_header 要不要含声明一起返回
* @return
*/  
  public static function toXml($data, $root = 'xml',$return_header=FALSE){
    $xml = new SimpleXMLExtended("<?xml version='1.0' encoding='utf-8'?><{$root} />");
    self::node($data,$xml);
    $xmlstr = $xml->asXML();
    $xmlstr = preg_replace('@<\?.*?>@','',$xmlstr);
    return trim($xmlstr);
    }
}