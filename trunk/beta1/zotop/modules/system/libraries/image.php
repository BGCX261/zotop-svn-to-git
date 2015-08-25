<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * image操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class image_base
{
	
	/**
	 * 获取获取指定大小的图片url
	 * @param string $image 图片文件名称
	 * @return string
	 */
	public static function url($image, $width=0, $height=0, $newext='')
	{
		if ( $width === 0 )
		{
			return $image;
		}
		
		//计算相应宽度高度的图片地址
		$ext = file::ext($image);
		$img = substr($image, 0, strlen($image)-strlen($ext)-1);
		$img = $img.'_'.$width;

		if ( $height !== 0 )
		{
			$img = $img.'_'.$height;
		}
		
		//新的扩展名
		if( !empty($newext) )
		{
			$ext = $newext;
		}
		
		//合成完整的图片url
		$img = $img.'.'.$ext;

		return $img;
	}

    public static function info($image) 
	{
        $image = ZOTOP_PATH_ROOT.DS.$image;

		$i = @getimagesize($image);

        if($i === false)
		{
			return false;
		}

		$size = @filesize($image);

		$info = array(
				'width'=>$i[0],
				'height'=>$i[1],
				'type' => $i[2],
				'size'=>$size,
				'mime'=>$i['mime']
				);

		return $info;
    }

	public static function resize($image, $width=0, $height=0)
	{
	
	}


}
?>