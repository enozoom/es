<?php
/*
 * 日志
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月24日下午5:52:23
 * 
 * 使用了以下日志等级
 * debug  调试模式
 * error  程序错误
 * info   管理员操作及相关SQL语句
 * notice 用户注册，下单，取消订单，推荐客户等
 * alert  网站受到恶意攻击
 */
namespace es\core\Log;

use es\libraries\Psr\Log\LoggerInterface;
use es\libraries\Psr\Log\LoggerTrait;
use es\libraries\Psr\Log\LogLevel;
use es\core\Toolkit\FileStatic;
use es\core\Toolkit\TimeStatic;

class Logger implements LoggerInterface{
  use LoggerTrait;
  
  private static $instance;
  private function __construct()
  {
      self::$instance =& $this;
  }
  
  public static function &getInstance()
  {
      empty(self::$instance) && new Logger();
      return self::$instance;
  }
  
  /**
   * 产生一条日志
   * 占位符使用'{}'
   * {@inheritDoc}
   * @see \es\libraries\Psr\Log\LoggerInterface::log()
   */
  public function log($level, $message, array $context = []){
      $file = $this->filePath( $level );
      $msg = '';
      switch ($level) {
        case LogLevel::DEBUG:// 调试可以打印对象
          ob_start();
              var_dump($message);
              $message = ob_get_contents();
          ob_end_clean();
          
        case LogLevel::ERROR:// 堆栈情况
          $debugs = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
          $trace = '';
          foreach($debugs as $i=>$t){
            if( $i > 1 && $i < count($debugs)-4 ){
              empty($t['file']) && $t['file'] = '控制器'.$t['class'];
              $_line = '**';
              for($j=0;$j<$i;$j++) $_line .= '>';
              $trace .= $_line.$t['file'].'，'.(empty($t['line'])?'':$t['line'].'行，').'方法'.$t['function'].PHP_EOL;
            }
          }
          
          $message = '【'.TimeStatic::formatTime().'】'.
                     PHP_EOL.$message.
                     PHP_EOL.$trace.
                     PHP_EOL.PHP_EOL;
          
        break;case LogLevel::ALERT:// 报警
          $message = '【'.TimeStatic::formatTime().'】'.PHP_EOL.$message.PHP_EOL.PHP_EOL;
        default:
          $message = $this->interpolate($message,$context);
        break;
      }
      FileStatic::write($message, $file);
  }
  
  
  /**
   * 替换占位符
   * @param string $message
   * @param array $context
   */
  public function interpolate($message, array $context = array())
  {
      $replace = [];
      foreach ($context as $key => $val) {
          $replace['{' . $key . '}'] = $val;
      }
      return strtr($message, $replace);
  }
  
  private function filePath($level=LogLevel::ERROR){
      return FileStatic::mkdir('logs/'.$level,1).date('Y-m-d').'.log';
  }
  
}