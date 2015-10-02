<?php  defined('APPPATH') OR exit('POWERED BY Enozoomstudio');

if(!function_exists('tag_img')){
/**
 * img标签
 * @param string $src
 * @param string $alt
 * @param bool $print 是否直接输出
 * @return string
 */	
	function tag_img($src,$alt='',$print=TRUE){
		$html = sprintf('<img src="%s" alt="%s" />',$src,$alt);
		if($print) echo $html;
		return $html;
	}
}

if(!function_exists('tag_a')){
/**
 * a标签
 * @param string $txt
 * @param string $href
 * @param bool $print 是否直接输出
 * @param bool $traget 是否打开新的页面
 * @return string
 */	
	function tag_a($txt,$href='#',$print=TRUE,$target=FALSE){
		global $configs;
		$html = sprintf('<a href="/">%s</a>',$txt);
		$href==='/'||$html = sprintf('<a href="%s.%s"%s>%s</a>',$href,$configs->config->suffix,$target?'target="_blank"':'',$txt);
		// 站外链接不需要加后缀2015年6月21日18:06:09
		stripos($href, 'http://')===FALSE||$html = str_replace('.'.$configs->config->suffix, '', $html);
		if($print) echo $html;
		return $html;
	}
}
