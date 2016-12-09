<?php 
namespace es\libraries\Wechat;
/**
* 微信官方
* 消息的加解密
* @author Joe e@enozoom.com
* 2015年10月9日 上午8:45:26
*/
use es\core\Toolkit\StrStatic;
class CryptStatic{
    public static $block_size = 32;
  
/**
 * 微信提供
 * 对需要加密的明文进行填充补位
 * @param $text 需要进行填充补位操作的明文
 * @return 补齐明文字符串
 */
  public static function PKCS7Encoder_encode($text) {
      $block_size = self::$block_size;
      $text_length = strlen($text);
      //计算需要填充的位数
      $amount_to_pad = self::$block_size - ($text_length % self::$block_size);
      if($amount_to_pad == 0) {
          $amount_to_pad = self::block_size;
      }
      //获得补位所用的字符
      $pad_chr = chr($amount_to_pad);
      $tmp = "";
      for($index = 0; $index < $amount_to_pad; $index++) {
          $tmp .= $pad_chr;
      }
      return $text . $tmp;
  }
  
/**
 * 微信提供
 * 对解密后的明文进行补位删除
 * @param decrypted 解密后的明文
 * @return 删除填充补位后的明文
 */
  public static function PKCS7Encoder_decode($text){
      $pad = ord(substr($text, -1));
      if($pad < 1 || $pad > 32){
          $pad = 0;
      }
      return substr($text, 0, (strlen($text) - $pad));
  }
  
/**
 * 加密消息的钥匙
 * @param string $encodingAesKey
 * @return string
 */
  public static function key($encodingAesKey){
      return base64_decode($encodingAesKey . "=");
  }
  
/**
 * 微信提供
 * 对加密消息进行解密
 * @param string $encrypted      加密消息字符串，是xml中的<Encrypt>
 * @param string $corpid
 * @param string $encodingAesKey 
 * @return SimpleXMLElement object
 */
  public static function decrypt($encrypted, $corpid, $encodingAesKey){
      $obj = null;
      $key = self::key($encodingAesKey);
      try {
          //使用BASE64对需要解密的字符串进行解码
          $ciphertext_dec = base64_decode($encrypted);
          $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
          $iv = substr($key, 0, 16);
          mcrypt_generic_init($module, $key, $iv);
      
          //解密
          $decrypted = mdecrypt_generic($module, $ciphertext_dec);
          mcrypt_generic_deinit($module);
          mcrypt_module_close($module);
      }catch (\Exception $e) {
          die('消息体解密错误');
      }
  
    try {
      $result = self::PKCS7Encoder_decode($decrypted);
      if (strlen($result) >= 16){
        $content = substr($result, 16, strlen($result));
        $len_list = unpack("N", substr($content, 0, 4));
        $xml_len = $len_list[1];
        $xml_content = substr($content, 4, $xml_len);
        $from_corpid = substr($content, $xml_len + 4);
      }
    } catch (\Exception $e) {
      die('消息体自身非法');
    }
    
    if( $from_corpid != $corpid ){
        die('corpid不匹配');
    }
    
    if( strpos($xml_content, '<')===FALSE ){
        return $xml_content;
    }
    
    $obj = simplexml_load_string($xml_content,'SimpleXMLElement', LIBXML_NOCDATA);
    return $obj;
  }
  
/**
 * 对消息进行加密
 * 微信提供
 * @param string $text           需要加密的消息
 * @param string $appid          
 * @param string $encodingAesKey
 * @return string 加密后的消息字符串
 */
  public static function encrypt($text, $appid, $encodingAesKey){
      $encrypted = '';
      $key = self::key($encodingAesKey);
      try {
          $random = StrStatic::randomString('alnum',16);
          $text = $random . pack("N", strlen($text)) . $text . $appid;
          $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
          $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
          $iv = substr($key, 0, 16);
          $text = self::PKCS7Encoder_encode($text);
          mcrypt_generic_init($module, $key, $iv);
          $encrypted = mcrypt_generic($module, $text);
          mcrypt_generic_deinit($module);
          mcrypt_module_close($module);
      }catch (\Exception $e) {
          die($text);
      }
      return base64_encode($encrypted);
  }

}