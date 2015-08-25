<?php
class BasePath
{
	/* 将 类似system.io.path的路径 还原成原始路径，但是必须是从站点根目录开始的路径
	 *
	 * @parem string $pathname 路径名称
	 * @parem string $ext      扩展名称
	 * @return string
	 */
	public static function decode($pathname,$ext='.php')
	{
		$pathname = explode('.',$pathname);
		$path = WWWROOT.DS.implode(DS,$pathname).$ext;
		$path = self::clean($path);
		return $path;
	}

	public static function encode($path,$ext='.php')
	{
		$path = self::clean($path);
		if(strtolower($ext) == strtolower(substr($path , -strlen($ext) ,strlen($ext))))
		{
			$path = substr($path , 0 , (strlen($path)-strlen($ext)));
		}
		$path = substr($path , strlen(WWWROOT));
		$path = trim($path , DS);
		$path = str_replace(DS , '.' , $path);
		return $path;
	}

	public static function clean($path,$ds='')
	{
		$path = trim($path);
		$ds = empty($ds) ? DS : $ds;
		if(empty($path))
		{
			return WWWROOT;
		}
		//$path = realpath($path);
		return preg_replace('#[/\\\\]+#', $ds, $path); //清理并转化
	}

	public static function find($paths,$file)
	{
		$paths = (array)$paths;
		foreach($paths as $path)
		{
			$fullname=$path.DS.$file;

		}
    }
}
?>