<?php
class BaseString extends Base
{
	public static function cut()
	{
		echo 'string::run';
	}

	/*
	 * 格式化字符串或者数组，去掉首尾空白字符与空白的字符串项
	 *
	 * $input='item1, item2, ,item3';
	 * $output=string::split($input,',');
	 * //$output 现在是一个数组
	 * //$output = array('item1','item2','item3');
	 *
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