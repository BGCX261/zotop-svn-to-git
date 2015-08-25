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
class file_base
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
	 * 获取文件的完整url
	 *
	 * @param string $url
	 * @return string
	 */
	public static function url($url, $domain=false)
	{
		//如果不是完整的链接，如：http://www.zotop.com/a/b/1.gif ，则将相对连接处理成绝对链接
	    if( strpos($url, '://') === false && $url[0]!='/' && $url[0] != '$' )
		{
		    $url = $domain ? '$domain/$root'.$url : '$root/'. $url;
		}		
		//解析url中的特殊字符串
		$url = url::decode($url);


		return $url;
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

	public static function size($file,$format=false)
	{
		$file = path::decode($file);
		$size = @filesize($file);
		if ($format)
		{
			$size = format::byte($size,$format);
		}
		return $size;
	}

	/**
	 * 获取文件的类型
	 * @param string $file
	 * @return string
	 */
	public static function type($file)
	{
		$ext = strpos($file, '.') === false ? $file : file::ext($file);
		$exts = file::exts();
		$type = 'unknown';
		
		foreach($exts as $t=>$e)
		{
			if ( preg_match('/^('.$e.')$/', $ext) )
			{
				$type = $t;
				break;
			}		
		}
		
		return $type;
	}

	public function exts($type='')
	{
		$exts = array(			
			'text' => 'html|htm|txt|php|asp|js|css|htc|tml|config|module|data|sql',
			'document' => 'doc|docx|xlt|xls|xlt|xltx|mdb|chm',
			'image' => 'jpg|jpeg|png|gif|bmp|ico|tif|tiff|psd|xbm|xcf',
			'video' => 'mpg|mpeg|avi|rm|rmvb|mov',
			'audio' => 'mp3|mp4|wav|wmv|midi|ra|ram',
			'flash' => 'swf|flv',
			'zip' => 'rar|zip|7z|tar',
		);

		$exts = zotop::filter('zotop.file.exts',$exts);

		if ( empty($type) )
		{
			return $exts;	
		}

		if ( isset($exts[$type])  )
		{
			return $exts[$type];
		}

		return $exts;			
	}

	public function types()
	{
		$types = array(			
			'text' => zotop::t('文本'),
			'document' => zotop::t('文档'),
			'image' => zotop::t('图像'),
			'video' => zotop::t('视频'),
			'audio' => zotop::t('音频'),
			'flash' => zotop::t('flash'),
			'zip' => zotop::t('压缩文件'),
			'unknown' => zotop::t('其它')
		);

		$types = zotop::filter('zotop.file.types',$types);

		return $types;
	}

	/*
	public static function editor($file)
	{
		$editor = array(
			'image'=>zotop::url('zotop/image/editor',array('file'=>$file)),
			'text'=>zotop::url('zotop/file/editor',array('file'=>$file)),
		);

		$editor = zotop::filter('zotop.file.editor',$editor);

		$type = file::type($file);
		
		if ( isset($editor[$type]) )
		{
			return $editor[$type];
		}

		return false;
	}
	*/


	/**
	 * 获取文件编码
	 *
	 * @param string $file
	 * @return string
	 */
	public static function isUTF8($file)
	{
		$str = file::read($file);

		if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32"))
		{
			return true;
		}
		else
		{
			return false;
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
	 * Strip close comment and close php tags from file headers used by WP
	 *
	 * @param string $str
	 * @return string
	 */
	public static function cleanup_header_comment($str) {
		return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
	}

	/**
	 * 读取文件的信息
	 *
	 * @param string $file
	 * @return string
	 */	
	public static function data($file, $headers=array(), $context = '')
	{
		$file = path::decode($file);
		
		if ( empty($headers) )
		{
			$headers = array(
				'name'=>'name',
				'title'=>'title',
				'description'=>'description',
				'author'=>'author',
				'url'=>'url',
			);
		}
		
		//读取文件的头部8KB
		$fp = fopen( $file, 'r' );
		$data = fread( $fp, 8192 );
		fclose( $fp );
		
		foreach ( $headers as $field => $regex )
		{
			preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $data, ${$field});

			if ( !empty( ${$field} ) )
			{
				${$field} = file::cleanup_header_comment( ${$field}[1] );
			}
			else
			{
				${$field} = '';
			}
		}

		$data = compact( array_keys( $headers ) );

		return $data;		
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
	    if ( !folder::exists(dirname($file)) )
		{
			folder::create(dirname($file));
		}

		if ( !file::exists($file) || $overwrite )
		{
		    return @file_put_contents($file, $content);
		}
		
        return false;
	}


	/**
	 * 删除文件或者文件组
	 *
	 * @param string|array $file 
	 * @return boolean
	 */
	public static function delete($file)
	{
		
		if ( is_array($file) )
		{
			$files = $file;
		} else {
			$files[] = $file;
		}

		foreach( $files as $file )
		{
			$file = path::decode($file);

			//尝试设置文件为可以读写删除
			@chmod($file, 0777);
			
			//删除文件
			if ( @unlink($file) )
			{
				//删除成功
			}
			else
			{
				zotop::error(zotop::t('删除文件失败 "{$file}"',array('file'=>$file)));
				return false;
			}
		}

		return true;
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
        return folder::files($path,$recurse,$fullpath,$ext,$ignore);
	}

	public static function copy()
	{
	    return false;
	}


    /**
     * 文件移动
     *
     * @param string $file  文件路径
     * @param string $path 新文件位置，不包含文件名称
	 *
     * @return bool
	 * @since 0.1
     */	
	public static function move($file,$path)
	{
		$file = path::decode($file);
		$name = file::name($file);
		$target = $path.DS.$name;

		//检查文件是否允许读写
		if (!is_readable($file) && !is_writable($file)) {
			zotop::error(zotop::t('未能找到原文件'));
		}

		//移动文件
		if ( !@rename($file, $target)) {
			zotop::error(zotop::t('移动失败'));
			return false;
		}

        return false;
	}


    /**
     * 文件重命名
     *
     * @param string $file  文件路径
     * @param string $newname 新文件名称，含文件扩展名
	 *
     * @return bool
	 * @since 0.1
     */	
	public static function rename($file,$newname)
	{
		$file = path::decode($file);
		$path = dirname($file);
		$newfile = $path.DS.file::safename($newname);

		if ( $file == $newfile )
		{
			zotop::error(zotop::t('目标文件名称和原文件名称相同'));
			return false;
		}
		elseif ( file::exists($newfile) )
		{
			zotop::error(zotop::t('目标文件已经存在'));
			return false;
		}
		elseif ( rename($file,$newfile) )
		{
			return true;
		}

		return false;
	}

	/**
	 * 返回安全的文件名称，去掉特殊字符
	 *
	 * @param string $file The name of the file [not full path]
	 * @return string The sanitised string
	 * @since 0.1
	 */
	public static function safename($file) {
		$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');
		return preg_replace($regex, '', $file);
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