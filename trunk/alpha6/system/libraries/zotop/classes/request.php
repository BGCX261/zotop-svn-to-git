<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * REQUEST
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
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