<?php
class zotop_format
{
	public static function byte($size,$len=2)
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