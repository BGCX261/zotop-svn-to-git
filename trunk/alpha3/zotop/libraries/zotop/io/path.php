<?php
class zotop_path
{
	/* path解析
	 *
	 * @parem string $path 路径名称
	 * @return string
	 */
	public static function decode()
	{

	}

	public static function encode()
	{

	}

	public static function clean($path,$ds='')
	{
		$path = trim($path);
		$ds = empty($ds) ? DS : $ds;
		if(empty($path))
		{
			return ZOTOP;
		}
		//$path = realpath($path);
		return preg_replace('#[/\\\\]+#', $ds, $path); //清理并转化
	}
}
?>