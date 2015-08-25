<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 字符串操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_string
{
	public static function substr($str, $start, $length, $charset='utf-8')
	{
		if(function_exists("mb_substr"))
		{
			return mb_substr($str, $start, $length, $charset);
		}
		elseif(function_exists('iconv_substr'))
		{
			return iconv_substr($str,$start,$length,$charset);
		}
		$regex['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$regex['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$regex['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$regex['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($regex[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
		return $slice;
	}

	public static function len($str, $charset='utf-8')
	{

	}


	/*
	 * 格式化字符串或者数组，去掉首尾空白字符与空白的字符串项
	 *
	 * $input='item1, item2, ,item3';
	 * $output=string::split($input,',');	 *
	 * $output 现在是一个数组
	 * $output = array('item1','item2','item3');
	 *
	 * @return array
	 */
	public static function split($input , $delimiter = ',' , $trim=true , $empty=false)
	{
        if (!is_array($input)){
            $input = explode($delimiter, $input);
        }
		if ($trim){
        	$input = array_map('trim', $input);
		}
		if (!$empty){
			$input = array_filter($input, 'strlen');
		}
		return $input;
	}
}
?>