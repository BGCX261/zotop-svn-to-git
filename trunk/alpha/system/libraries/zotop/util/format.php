<?php
class BaseFormat extends Base
{
	public static function size($size,$len=2)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		$pos=0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}
		return number_format($size,$len).' '.$units[$pos];
	}


}
?>