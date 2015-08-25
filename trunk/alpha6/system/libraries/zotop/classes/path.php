<?php
class zotop_path
{
	/* path解析
	 *
	 * @parem string $path 路径名称
	 * @return string
	 */
	public static function decode($path)
	{
        
	    $p = array(
	        '$system' => ZOTOP_PATH_SYSTEM,
	    	'$modules' => ZOTOP_PATH_MODULES,
	    );
	    $path = strtr($path,$p);
	    $path = path::clean($path);
	    return $path;
	}

	/**
	 * 将真实的path转化为系统的path表示方法
	 * 
	 *
	 */
	public static function encode($path)
	{
        return $path;
	}
    
	
	/**
	 * 清理路径中多余的斜线
	 * 
	 *
	 */
	public static function clean($path,$ds='')
	{
		$ds = empty($ds) ? DIRECTORY_SEPARATOR : $ds;
		
	    $path = trim($path);
		$path = empty($path) ? ZPATH_ROOT : $path;
		return preg_replace('#[/\\\\]+#', $ds, $path); //清理并转化
	}
}
?>