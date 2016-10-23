<?php
namespace es\core\Toolkit;

use es\core\Toolkit\Curl;
trait CaptchaTrait
{
  use Curl;
  /**
   * 发送验证码
   * @param string $mobile
   * @param string $smstpl
   */
  private function sendCaptcha_($mobile,$smstpl)
  {
    
  }
  /**
   * 验证验证码
   * @param string $mobile
   * @param string $smstpl
   * @param string $code
   */
    private function checkCaptcha_($mobile,$smstpl,$code)
  {
    
  }
  
}