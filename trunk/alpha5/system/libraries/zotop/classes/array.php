<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数组操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_arr
{
	/**
	 * 从数组中弹出键，类似于array_pop,但是是根据键名弹出
	 *
	 * @param string $key 弹出的键名
	 * @param array $array 目标数组
	 * @param boolean $bool 是否区分大小写
	 * @return $mix	被弹出 的数据
	 */
	public static function take($key,&$array,$bool=TRUE)
	{
		$array = (array)$array;
		if($bool)
		{
			$key=strtolower($key);
			$array=array_change_key_case($array);
		}

		if(array_key_exists($key,$array))
		{
			$str=$array[$key];
			unset($array[$key]);
			return $str;
		}
		return NULL;
	}

	/**
	 * 将字符串转化为数组
	 *
	 * @param string $key 弹出的键名
	 * @param array $array 目标数组
	 * @param boolean $bool 是否区分大小写
	 * @return $mix	被弹出 的数据
	 */
	public static function decode($array,$s1 = "\n", $s2 = '|')
	{
		$options = explode($s1, $array);
		foreach($array as $option)
		{
			if(strpos($option, $s2))
			{
				list($name, $value) = explode($s2, trim($option));
			}
			else
			{
				$name = $value = trim($option);
			}
			$os[$value] = $name;
		}
		return $os;
	}

	public static function trim($input)
	{
		if (!is_array($input) )
		{
			return trim($input);
		} 
		return array_map(array('arr','trim'), $input);
	}
}
?>