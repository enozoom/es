<?php
namespace app\controllers\common;

use es\core\Controller\DataController;

final class min extends DataController{
    private $cache = FALSE;
    private $cache_dir;
    private $cache_suffix = '.cjss';
    private $def_dir;
    private $admin_dir;

    public function __construct()
    {
        parent::__construct();
        $this->cache_dir = APPPATH.'cache/cssjs/';
        $this->def_dir = './theme/'.$this->getConfig('theme_path').'/';
        $this->admin_dir = APPPATH.'data/esadmin/';
    }
 
    public function index($files='')
    {
        $output = $this->cache($files);
        $suffix = substr($files,strrpos($files,'.')+1);// 获取后缀
        empty($output) || $this->compress($output,$suffix);
    }

    /**
     * 对合并文件进行css输出
     * @param string $str
     * @return string
     */
    private function _css($str='')
    {
        $str = preg_replace(array('/{\s*([^}]*)\s*}/','/\s*:\s*/','~\/\*[^\*\/]*\*\/~s'),array('{$1}',':',''),$str);
        $str = preg_replace(array('/'.PHP_EOL.'/','/\n*/'),'',$str);
        return $str;
    }

    /**
     * 对合并文件进行js输出
     * @param string $str
     * @return string
     */
    private function _js($str='')
    {
        return $str;
    }

    /**
     * 压缩字符串
     * @param string $str
     * @return void 直接输出到页面
     */
    private function compress($str,$suffix='css')
    {
        $this->render($str,$suffix);
    }

    /**
     * 读取或将合并文件包含字符写入缓存
     * @param string $files
     * @return string
     */
    private function cache($files='')
    {
        $output = '';
        if($this->cache){// 从缓存中读取
            $filename = sha1($files).$this->cache_suffix;
            $path = './'.$this->cache_dir.$filename;
            file_exists($path) && $output = file_get_contents($path);
        }
        $filetype = substr($files,strrpos($files,'.')+1);// 获取后缀
        
        if(empty($output)){// 从文件中读取
            $files = str_replace(".{$filetype}",'',$files);// 去后缀
            foreach(explode(',',$files) as $f){
                $dir = stripos($f, 'esadmin') === FALSE ? $this->def_dir : $this->admin_dir;
                $path = $dir.$filetype."/{$f}.".$filetype;
                file_exists($path) && $output .= file_get_contents($path);
            }
            if(!empty($output)){
                $method = "_{$filetype}";
                $output = $this->$method($output);
                $this->cache &&
                file_put_contents($this->cache_dir.$filename, $output);
            }
        }
        
        return $output;
    }
}