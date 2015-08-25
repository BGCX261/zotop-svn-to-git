<?php
class BaseFolder extends Base
{

	function exists($path)
	{
		return is_dir(path::clean($path));
	}

	//创建文件夹，返回true或者false
	public static function create($dir, $mode = 0755)
	{
	  if(is_dir($dir) || @mkdir($dir,$mode))
	  {
		  return true;
	  }
	  if(!self::create(dirname($dir),$mode))
	  {
		  return false;
	  }
	  return @mkdir($dir,$mode);
	}
	//删除文件夹
	public static function delete($path)
	{

	}
	//浏览文件夹，返回目录下的全部文件夹的数组
	public static function brower($path,$filter='',$level=0)
	{

	}
}
?>