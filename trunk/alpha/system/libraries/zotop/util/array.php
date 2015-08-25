<?php
class BaseArr extends Base
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
}
?>