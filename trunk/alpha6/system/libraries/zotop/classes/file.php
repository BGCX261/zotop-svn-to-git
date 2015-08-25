<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * file操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_file
{
	
	/**
	 * 获取文件的扩展名
	 * @param string $file 文件名称
	 * @return string
	 */
	public static function ext($file)
	{
		$dot = strrpos($file, '.') + 1;
		
		return substr($file, $dot);
	}

	/**
	 * 获取文件名称 , 第二个参数控制是否返回文件扩展名，默认为返回带扩展名的文件名称
	 * @param string $file
	 * @param boolean $stripext
	 * @return string
	 */
	public static function name($file,$stripext=false)
	{
		$name=basename($file);
		if ( $stripext )
		{
			$ext = file::ext($file);
			$name=basename($file,'.'.$ext);
		}
		return $name;
	}

	/**
	 * 判断文件是否存在
	 * @param string $file
	 * @return boolean
	 */
	public static function exists($file)
	{
	    if ( empty($file) ) return false;
	    
	    $file = path::decode($file);
	    
	    return @is_file($file);
	}

	/**
	 * 获取文件的类型
	 * @param string $file
	 * @return string
	 */
	public static function type($file)
	{
		$ext = file::ext($file);
		
		if ( preg_match('/^(jpe?g|png|[gt]if|bmp|ico|tif|tiff|psd|xbm|xcf)$/', $ext) )
		{
			$type = 'image';
		}
		elseif ( preg_match('/^(doc|docx|xlt|xls|xlt|xltx|mdb|chm)$/', $ext) )
		{
			$type = 'document';
		}
		elseif ( preg_match('/^(html|htm|txt|php|asp|js|css|htc|tml|config|module|data|sql)$/', $ext) )
		{
			$type = 'text';
		}
		elseif ( preg_match('/^(rar|zip|7z|tar)$/', $ext) )
		{
			$type = 'zip';
		}
		elseif ( preg_match('/^(swf|flv)$/', $ext) )
		{
			$type = 'flash';
		}
		elseif ( preg_match('/^(mp3|mp4|wav|wmv|midi|ra|ram)$/', $ext) )
		{
			$type = 'audio';
		}
		elseif ( preg_match('/^(mpg|mpeg|avi|rm|rmvb|mov)$/', $ext) )
		{
			$type = 'video';
		}
		else
		{
			$type = 'unknown';
		}
	}


	/**
	 * 读取文件内容
	 *
	 * @param string $file
	 * @return string
	 */
	public static function read($file)
	{
       $file = path::decode($file);
       
       return @file_get_contents($file);
	}


	/**
	 * 写入文件
	 *
	 * @param string $file
	 * @param string $content
	 * @param boolean $overwrite
	 * @return boolean
	 */
	public static function write($file, $content='', $overwrite=TRUE)
	{
	    $file = path::decode($file);
	    
	    //当目录不存在的情况下先创建目录
	    if ( !dir::exists(dirname($file)) )
		{
			dir::create(dirname($file));
		}

		if ( !file::exists($file) || $overwrite )
		{
		    return @file_put_contents($file, $content);
		}
		
        return false;
	}


	/**
	 * 删除文件
	 * @param string $file
	 * @return boolean
	 */
	public static function delete($file)
	{
        $file = path::decode($file);
        return @unlink($file);
	}

	/**
	 * 返回目录下的全部文件的数组,当level为0时候返回全部子文件夹目录
	 * @param string $path 路径
	 * @param array $ext 特定的文件格式,如只获取jpg,png格式
	 * @param bool|int $recurse 子目录，或者子目录级数
	 * @param bool $fullpath 全路径或者仅仅获取文件名称
	 * @param array $ignore 忽略的文件夹名称
	 * @return array
	 */
	public static function brower($path, $ext='', $recurse=false, $fullpath=false, $ignore = array('.svn', 'CVS','.DS_Store','__MACOSX'))
	{
        return dir::files($path,$ext,$recurse,$fullpath,$ignore);
	}

	public static function copy()
	{
	    return false;
	}

	public static function move()
	{
        return false;
	}

    /**
     * 上传文件
     *
     * @param string $field  FILE字段名称
     * @param array|string $params 上传的参数|上传文件名称
     * @return array
     */
    public static function upload($field,$params)
    {
		if( is_array($params) )
		{
			
		}
	}

    /**
     * 远程下载文件
     *
     * @param string $url  远程文件地址
     * @param array $params 下载的参数
     * @return array
     */
	public static function download($url,$params)
	{
	
	
	}


	public static function find()
	{

	}
	
	public static function compile($file)
	{
	    $content = file::read($file);
	    $content = trim($content);
	    //strip <?php	    

	    $content = substr($content,5); 
	    //strip <?php
		if( strtolower(substr($content,-2)) == '?>' )
	    {
	       $content = substr($content,0,-2); 
	    }
	    return $content;	    
	}
}
?>