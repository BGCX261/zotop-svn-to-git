<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * dir类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class folder_base
{

	/**
	 * 判断目录是否 存在
	 *
	 */
	public static function exists($dir)
	{
		return is_dir( path::decode($dir) );
	}

	
	/**
	 * 返回文件夹的大小
	 */
	public static function size($dir)
	{
		$dir = path::decode($dir);
		
	    $handle = @opendir($dir);
        $size = 0;
        
		while (false!==($f = readdir($handle)))
        {

			if($f != "." && $f != "..")
            {
                if(is_dir("$dir/$f"))
                {
                    $size += folder::size("$dir/$f");
                }
                else
                {
                    $size += filesize("$dir/$f");
                }
            }
        }
        
        @closedir($handle);
        return $size;
	}

	/**
	 * 创建文件夹，返回true或者false
	 */
	public static function create($dir, $mode = 0755)
	{
	    
	  $dir = path::decode($dir);
	  
	  if ( is_dir($dir) || @mkdir($dir,$mode) )
	  {
		  return true;
	  }
	  
	  if( !folder::create(dirname($dir),$mode) )
	  {
		  return false;
	  }
	  
	  return @mkdir($dir,$mode);
	}
	
	/**
	 * 清理文件夹中全部文件
	 */
	public static function clear($dir, $subfolder= true)
	{
	    $dir = path::decode($dir);

	    $files = (array)folder::files($dir);
        
        foreach($files as $file)
        {
           @unlink($dir.DS.$file);
        }
		
		return true;
	}
	
	/**
	 * 清理文件夹中全部文件及文件夹
	 */
	public static function clean($path)
	{
		 return folder::delete($path, false);
	}

	/**
	 * 删除文件夹
	 */
	public static function delete($path, $deleteSelf = true)
	{
	    $path = path::decode($path);
		
		// 判断是否是文件夹
		if ( !is_dir($path) ) 
		{
			return false;
		}
		
		// 删除文件夹下的全部文件

		$files = folder::files($path, false, true, '', array());
		if ( count($files) )
		{
			if (file::delete($files) !== true) {
				return false;
			}
		}

		// 删除全部子文件夹
		$folders = folder::folders($path, false, true, '.', array());
		foreach ( $folders as $folder ) {
			if (folder::delete($folder) !== true) {
				return false;
			}
		}
		
		//删除自身，如果不删除自身，则为清理文件夹
		if ( $deleteSelf === true )
		{
			if (@rmdir($path))
			{
				return true;
			}
			return false;
		}
        
		return true;
	}
    
	/**
	 * 返回目录下的全部文件的数组
	 * @param string $path 路径
	 * @param array $ext 特定的文件格式,如只获取jpg,png格式
	 * @param bool|int $recurse 子目录，或者子目录级数
	 * @param bool $fullpath 全路径或者仅仅获取文件名称
	 * @param array $ignore 忽略的文件夹名称
	 * @return array
	 */
	public static function files($path, $ext='', $recurse=false, $fullpath=false, $ignore = array('.svn', 'CVS','.DS_Store','__MACOSX'))
	{

    	$files =array();
    	
	    $path = path::clean($path);
	    
	    if( !is_dir($path) )
	    {
	        return false;
	    }
	    
	    $handle = opendir($path);
	    
	    while (($file = readdir($handle)) !== false)
	    {
	        if( $file != '.' && $file != '..' && !in_array($file,$ignore) )
	        {
	            $f = $path .DS. $file;

	            if( is_dir($f) )
	            {
	                if ( $recurse )
	                {
    	                if( is_bool($recurse) )
    	                {
    	                    $subfiles = folder::files($f,$recurse,$fullpath,$ext);
    	                }
    	                else
    	                {
    	                    $subfiles = folder::files($f,$recurse-1,$fullpath,$ext);
    	                }
                    	if( is_array($subfiles) )
	                    {
	                        $files = array_merge($files,$subfiles);
	                    }    	                
	                }	                
	            }
	            else
	            {   
	                if( !empty($ext) )
	                {
	                    if( is_array($ext) && in_array(file::ext($file),$ext) )
	                    {
	                        $files[] = $fullpath ? $f :  $file;
	                    }
	                }
	                else
	                {
	                    $files[] = $fullpath ? $f :  $file;
	                }
	                
	            }
	        }
	    }
	    closedir($handle);
	    return $files;
	}
	/**
	 * 返回目录下的全部文件夹的数组
	 * @param string $path 路径
	 * @param array $filter 
	 * @param bool|int $recurse 子目录，或者子目录级数
	 * @param bool $fullpath 全路径或者仅仅获取文件名称
	 * @param array $ignore 忽略的文件夹名称
	 * @return array
	 */	
	public static function folders($path, $filter='.', $recurse=false, $fullpath=false, $ignore = array('.svn', 'CVS','.DS_Store','__MACOSX'))
	{
	    $folders = array();
	    
	    $path = path::clean($path);
	    
	    if( !is_dir($path) )
	    {
	       return false;
	    }
	    
	    $handle = opendir($path);
	    
	    while (($file = readdir($handle)) !== false)
	    {
	        $f = $path .DS. $file;
	        if( $file != '.' && $file != '..' && !in_array($file,$ignore) && is_dir($f) )
	        {
                if (preg_match("/$filter/", $file)) {
                	if ($fullpath) {
                		$folders[] = $f;
                	} else {
                		$folders[] = $file;
                	}
                }
	            if ($recurse) {
					if (is_integer($recurse)) {
						$recurse--;
					}
					$subfolders = folder::folders($f, $recurse, $fullpath, $filter,$ignore);
					$folders = array_merge($folders, $subfolders);
				}       
	        }	        
	    }
	    
	    closedir($handle);
	    
	    return $folders;	    
	}

	/**
	 * 格式化路径
	 *
	 * @access	public
	 * @param	string $path The full path to sanitise
	 * @return	string The sanitised string
	 */
	function check($path)
	{
		$ds		= ( DS == '\\' ) ? '\\'.DS : DS;
		$regex = array('#[^A-Za-z0-9:\_\-'.$ds.' ]#');
		return preg_replace($regex, '', $path);
	}
}
?>