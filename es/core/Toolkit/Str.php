<?php
/*
 * 字符串相关操作
 */
namespace es\core\Toolkit;

trait Str {
    public static function cleanStyleAndScript($htm){
        $filterStyleScript = preg_replace('/<(s(cript|tyle))[^>]*>([^<]*)<\/\1>/', '', self::cleanHtmlblank($htm));
        return preg_replace('/style\s*=\s*(\'|")[^\1]*?\1/', '', $filterStyleScript);
    }
    /**
     * 去html字符串和换行符
     * @param $str string 需要进行转换的含有html标签的字符串
     * @return string
     */
    public static function cleanHtmltag($str){
        return preg_replace(['/(<\/?)(\w+)([^>]*>)/',"/\n/","/\r\n/","/\r/"],'',$str);
    }
    
    /**
     * 去字与字之间的空白.
     * @param $str string
     * @return string
     */
    public static function cleanWordblank($str){
        $str = preg_replace('|(\s*)(\S+)(\s*)(\S+)(\s*)|','$2$4',$str);
        // 解决中文空格和换表符无法正确匹配的问题
        $str = str_replace(['  ','　','  ','　'], '', $str);
        return self::removeInvisibleCharacters($str);
    }
    
    /**
     * 去html的空白(标签间的空白和换行,对于非标签间的无能为力)
     * @param $str string
     * @return string
     */
    public static function cleanHtmlblank($str){
        return str_replace(PHP_EOL, ' ', preg_replace(['/\n/','/>\s*([^\s]*)\s*</', '/<!--[^\[>]*>/',"/\r\n/","/\r/"],
                            ['','>$1<','','',''],$str));
    }
    
    
    /**
     * 去除非法字符
     * @access public
     * @param  string $str
     * @return string $url_encoded
     */
    public static function removeInvisibleCharacters($str, $url_encoded = TRUE)
    {
        $non_displayables = [];
    
        if ($url_encoded){
            $non_displayables[] = '/%0[0-8bcef]/';  // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';  // url encoded 16-31
        }
    
        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';  // 00-08, 11, 12, 14-31, 127
    
        do{
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        }while ($count);
    
        return $str;
    }
    
    public static function cleanInvisibleCharacters($str){
        return preg_replace('/[^\P{C}\n]+/u', '', $str);
    }
    
    /**
     * 产生随机数
     * @param string $type alnum:数字+大小写字母;numeric:数字;alpha:大小写字母
     * @param int    $len
     *
     * @return string
     */
    public static function randomString($type='alnum',$len=6){
        $numeric = '0123456789';
        $alpha = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $alnum = $numeric.$alpha;
        $_str = [];
    
        $_type = $$type;
        $str = '';
        for($i=0; $i < $len; $i++){
            $str .= substr($_type, mt_rand(0, strlen($_type) -1), 1);
        }
        return $str;
    }
    
    /**
     * 伪造一个电话号码
     * @param bool 是否遮挡中间部分
     */
    public static function forgedMobile($defend=TRUE){
        return sprintf('1%d%s%s',
                       [3,5,7,8][mt_rand(0,3)].mt_rand(0,9),
                       $defend?'****':str_pad(mt_rand(1000,9999),4,'0',STR_PAD_LEFT),
                       str_pad(mt_rand(0,9999),4,'0',STR_PAD_LEFT));
    }
    
    /**
     * 判断是否是一个手机号
     * @param string $mobile
     */
    public static function isMobile($mobile=''){
        return preg_match('/^1[34578]\d{9}$/', $mobile);
    }
}