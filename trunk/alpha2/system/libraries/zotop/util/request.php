<?php
/*
 * 系统REQUEST方法
 *
 */
class zotop_request
{
	public static function get($name='', $valid = NULL, $default = NULL)
	{
		if(empty($name))
		{
			return $_GET;
		}
		$get = $_GET[$name];
		return trim($get);
	}

	public static function post($name ='', $valid = NULL, $default = NULL)
	{
		if(empty($name))
		{
			return $_POST;
		}
		$post = $_POST[$name];
		$post = is_string($post) ? trim($post) : $post;
		return $post;
	}

	public static function referer()
	{
		return $_SERVER['HTTP_REFERER'];
	}



}
?>
