<?php
/*
 * 文件相关操作
 */
namespace es\core\Toolkit;

class FileStatic {
  /**
   * 生成文件夹
   * 注意要生成的文件的位置权限是否可写
   * @param string $filepath 生成的路径
   * @param bool $timedir    是否开启时间格式文件夹名
   *
   * @return string 生成的文件路径
   */
  public static function mkdir($filepath,$timedir=FALSE)
  {
      $timedir = $timedir?date('/Y/m'):'';
      $filepath = str_replace('//','/',$filepath.$timedir);
      if(!file_exists($filepath)){
          mkdir($filepath,0777,1);
      }
      return $filepath.'/';
  }
  
  /**
   * 写入内容
   * @param string $str
   * @param strint $file
   */
  public static function write( $str,$file ){
    file_put_contents($file,$str,FILE_APPEND);
  }
  
  /**
   * 递归扫描文件夹，按条件获取文件夹路径
   * @param string $files
   * @param string $root
   * @param object $filter 过滤的方法，$filter($d)，会自动传入变量$d,文件夹名称
   */
  public static function scanDir(&$files,$root = './',$funcFilter=''){
      is_object($funcFilter) || $funcFilter = function($d){
          return true;
      };
      foreach (scandir($root) as $d){
          $_d = str_replace('//','/',"{$root}/{$d}");
          if( is_dir($_d) ){// 文件夹
              if(!in_array($d, array('.','..'))){
                  if( $funcFilter($d) ){
                      $files[$d] = $_d;
                  }
                  self::scanDir($files, $_d, $funcFilter);
                  continue;
              }
          }
      }
  }
  
  /**
   * 删除文件夹
   * @param string $dirPath
   */
  public static function delDir($dirPath){
      if(is_dir($dirPath)){
          $files = glob( $dirPath.'*', GLOB_MARK );
          foreach( $files as $file )
          {
              self::delDir( $file );
          }
          is_dir($dirPath) && rmdir( $dirPath );
      } elseif(is_file($dirPath)) {
          unlink( $dirPath );
      }
  }
  
  /**
   * 合并文件夹，如果存在同名文件，则使用$source下的文件。
   * @param string $source
   * @param string $target
   */
  public static function mergeDir($source, $target){
      $path = function($_path){
          $_path = preg_replace(['#\/#','#\\\\#'], DIRECTORY_SEPARATOR, $_path);
          return rtrim( $_path, DIRECTORY_SEPARATOR ).DIRECTORY_SEPARATOR;
      };
      $source = $path( $source );
      $target = $path( $target );
      
      self::mkdir( $target );
      // 搜索目录下的所有文件
      foreach( glob( $source . '*' ) as $filename ) {
          if(is_dir( $filename )) {
              self::mergeDir($filename, $target.basename( $filename ) );
          }elseif( is_file($filename) ) {
              copy( $filename, $target.basename( $filename ) );
          }
      }
  }
}